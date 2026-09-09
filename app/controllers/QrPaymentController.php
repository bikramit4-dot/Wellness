<?php
/**
 * Handles the public "Advance Payment by QR Code" form on the Tariff page.
 * Validates the input, saves the payment screenshot, stores the record and
 * notifies the admin by email (the admin panel also lists every submission).
 */
class QrPaymentController extends Controller
{
    /** Max submissions per IP inside the sliding window below. */
    private const RATE_MAX = 5;
    private const RATE_WINDOW = 600; // 10 minutes

    /** Upload cap (matches the admin image uploader). */
    private const MAX_UPLOAD_BYTES = 5 * 1024 * 1024;

    public function store(): void
    {
        if (!isset($_POST['csrf_token']) || !Security::validateCsrf($_POST['csrf_token'])) {
            $this->setFlash('danger', 'Invalid security token. Please try again.');
            $this->redirect('/tariff#qr-payment');
        }

        // Rate limiting: at most 5 real submissions per IP every 10 minutes,
        // so a bot can never flood the payments inbox. Limited callers get the
        // same "thank you" so they can't tell they were throttled.
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        if (Security::throttle('qr-payment:' . $ip, self::RATE_MAX, self::RATE_WINDOW) > 0) {
            error_log('[QrPayment] Rate limit hit for IP ' . $ip);
            $this->setFlash('success', 'Your advance payment details were received. We will confirm shortly.');
            $this->redirect('/tariff#qr-payment');
        }

        // Spam protection: honeypot field + minimum form-fill time. Bots are
        // silently "accepted" so they never learn they were caught.
        if ($this->looksLikeSpam()) {
            error_log('[QrPayment] Spam submission blocked.');
            $this->setFlash('success', 'Your advance payment details were received. We will confirm shortly.');
            $this->redirect('/tariff#qr-payment');
        }

        // Length caps keep the stored data tidy and stop oversized payloads.
        $name = mb_substr(Security::sanitizeText($_POST['full_name'] ?? ''), 0, 120);
        $email = mb_substr(Security::sanitizeEmail($_POST['email'] ?? ''), 0, 190);
        $phone = mb_substr(Security::sanitizeText($_POST['phone'] ?? ''), 0, 40);
        $address = mb_substr(Security::sanitizeText($_POST['address'] ?? ''), 0, 300);
        $package = mb_substr(Security::sanitizeText($_POST['package'] ?? ''), 0, 120);
        $amount = mb_substr(Security::sanitizeText($_POST['amount'] ?? ''), 0, 40);
        $transactionId = mb_substr(Security::sanitizeText($_POST['transaction_id'] ?? ''), 0, 120);
        $message = mb_substr(Security::sanitizeText($_POST['message'] ?? ''), 0, 1000);

        if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || $phone === '' || !preg_match('/^[+\d\s\-]{7,20}$/', $phone)
            || $address === '' || $package === '' || $amount === '' || $transactionId === '') {
            $this->setFlash('danger', 'Please provide your name, a valid email, phone number, address, selected package, amount and transaction / UTR number.');
            $this->redirect('/tariff#qr-payment');
        }

        // The payment screenshot is how the admin verifies the payment.
        $screenshot = $this->handleScreenshotUpload();
        if ($screenshot === '') {
            // Keep the specific reason (e.g. "too large", "wrong type") when
            // the upload handler already set one; otherwise show the generic hint.
            if (empty($_SESSION['flash'])) {
                $this->setFlash('danger', 'Please attach a clear screenshot of your payment (JPG, PNG, WEBP or GIF, up to 5 MB).');
            }
            $this->redirect('/tariff#qr-payment');
        }

        $model = new QrPaymentModel();
        $model->save($name, $email, $phone, $address, $package, $amount, $transactionId, $message, $screenshot);

        // In-app notification for the admin panel
        NotificationModel::add(
            'qr_payment',
            'New Advance Payment',
            $name . ' paid ' . $amount . ' for ' . $package . ' (TXN: ' . $transactionId . ')',
            '/admin/qr-payments'
        );

        // Email notification to admin (non-blocking)
        Mailer::sendQrPaymentNotification([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'package' => $package,
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'message' => $message,
            'screenshot' => $screenshot,
        ]);

        $this->setFlash('success', 'Thank you! Your advance payment details were received. We will verify the payment and confirm your booking shortly.');
        $this->redirect('/tariff#qr-payment');
    }

    /**
     * Save an uploaded payment screenshot into public/uploads/qr-payments/
     * and return its public URL, or '' on failure (a flash message is set).
     *
     * Uses a multi-strategy approach:
     *   1. move_uploaded_file (standard)
     *   2. copy + unlink (fallback for open_basedir / permission issues)
     *   3. file_put_contents with file_get_contents (last resort)
     */
    private function handleScreenshotUpload(): string
    {
        $upload = $_FILES['screenshot'] ?? null;
        if (!is_array($upload)) {
            error_log('[QrPayment] Upload: no file data in $_FILES');
            return '';
        }

        $uploadError = (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($uploadError !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit.',
                UPLOAD_ERR_FORM_SIZE  => 'File exceeds form upload limit.',
                UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
                UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Server missing temporary upload folder.',
                UPLOAD_ERR_CANT_WRITE => 'Server failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'Upload blocked by server extension.',
            ];
            $msg = $errorMessages[$uploadError] ?? 'Unknown upload error (code ' . $uploadError . ').';
            error_log('[QrPayment] Upload error: ' . $msg);
            $this->setFlash('danger', 'Upload failed: ' . $msg);
            return '';
        }

        if ((int) ($upload['size'] ?? 0) > self::MAX_UPLOAD_BYTES) {
            $this->setFlash('danger', 'Upload rejected: the screenshot is larger than 5 MB.');
            return '';
        }

        $tmpName = (string) ($upload['tmp_name'] ?? '');
        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            error_log('[QrPayment] Upload: tmp_name invalid or not an uploaded file');
            return '';
        }

        // MIME whitelist — sniff the actual file content, never trust the browser.
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];
        $finfo = function_exists('finfo_open') ? @finfo_open(FILEINFO_MIME_TYPE) : false;
        $sniffed = $finfo !== false
            ? (string) @finfo_file($finfo, $tmpName)
            : (string) ($upload['type'] ?? '');
        if ($finfo !== false) {
            @finfo_close($finfo);
        }
        $ext = $allowed[$sniffed] ?? '';
        if ($ext !== '' && @getimagesize($tmpName) === false) {
            $ext = '';
        }
        if ($ext === '') {
            error_log('[QrPayment] Upload: rejected MIME type "' . $sniffed . '"');
            $this->setFlash('danger', 'Upload rejected: only JPG, PNG, WEBP and GIF images are allowed.');
            return '';
        }

        // Find a writable directory to save the screenshot.
        // Try qr-payments/ first, then fall back to the main uploads/ folder.
        $filename = 'qr-pay-' . date('Ymd-His') . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dir = $this->findWritableDir($filename);
        if ($dir === '') {
            error_log('[QrPayment] Upload: no writable directory found');
            $this->setFlash('danger', 'Upload failed: could not create a writable uploads folder. Please contact support.');
            return '';
        }
        $dest = $dir . '/' . $filename;

        // Strategy 1: move_uploaded_file (works in most environments)
        $ok = @move_uploaded_file($tmpName, $dest);

        // Strategy 2: copy + unlink (works when open_basedir blocks move but not copy)
        if (!$ok) {
            error_log('[QrPayment] move_uploaded_file failed, trying copy+unlink');
            $ok = @copy($tmpName, $dest);
            if ($ok) {
                @unlink($tmpName);
            }
        }

        // Strategy 3: file_put_contents + file_get_contents (last resort)
        if (!$ok) {
            error_log('[QrPayment] copy failed, trying file_put_contents');
            $content = @file_get_contents($tmpName);
            if ($content !== false) {
                $ok = @file_put_contents($dest, $content) !== false;
                if ($ok) {
                    @unlink($tmpName);
                }
            }
        }

        if (!$ok) {
            $err = error_get_last();
            error_log('[QrPayment] Upload FAILED all strategies. Last error: ' . ($err['message'] ?? 'unknown') . ' dest=' . $dest);
            $this->setFlash('danger', 'Upload failed: could not save the screenshot. Please try a smaller image or a different browser.');
            return '';
        }

        @chmod($dest, 0644);

        // Auto-optimize: convert to WebP to save bandwidth and storage.
        $dest = ImageOptimizer::optimize($dest);
        $filename = basename($dest);

        error_log('[QrPayment] Upload OK: ' . $filename . ' (' . filesize($dest) . ' bytes) -> ' . $dir);

        // Build the public URL based on where the file actually ended up.
        $publicRoot = APP_ROOT . '/public';
        if (str_starts_with($dir, $publicRoot)) {
            return BASE_URL . '/public' . substr($dir, strlen($publicRoot)) . '/' . $filename;
        }
        // Fallback: saved outside public/ — return absolute path.
        return $dir . '/' . $filename;
    }

    /**
     * Find a writable directory for uploads, creating if needed.
     * Tries multiple strategies and locations.
     *
     * @return string absolute path to a writable directory, or '' on failure
     */
    private function findWritableDir(string $filename): string
    {
        // Candidate directories (most specific first)
        $candidates = [
            APP_ROOT . '/public/uploads/qr-payments',
            APP_ROOT . '/public/uploads',
            APP_ROOT . '/storage',
        ];

        foreach ($candidates as $dir) {
            // Try to create the directory if it doesn't exist
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
                // Also try without recursive
                if (!is_dir($dir)) {
                    @mkdir($dir, 0777);
                }
            }

            if (!is_dir($dir)) {
                continue;
            }

            // Try to fix permissions if not writable
            if (!is_writable($dir)) {
                @chmod($dir, 0777);
            }

            if (!is_writable($dir)) {
                continue;
            }

            // Verify we can actually write a test file
            $testFile = $dir . '/.write-test-' . bin2hex(random_bytes(4));
            if (@file_put_contents($testFile, 'ok') !== false) {
                @unlink($testFile);
                error_log('[QrPayment] Using writable dir: ' . $dir);
                return $dir;
            }
        }

        return '';
    }

    /**
     * Heuristic spam detection for the QR payment form.
     */
    private function looksLikeSpam(): bool
    {
        // 1. Honeypot: real users never see or fill this field.
        if (trim((string) ($_POST['website'] ?? '')) !== '') {
            error_log('[QrPayment] Spam: honeypot filled');
            return true;
        }

        // 2. Minimum form-fill time — use both session and POST timestamps.
        $sessionTs = (int) ($_SESSION['tariff_form_ts'] ?? 0);
        $postTs    = (int) ($_POST['form_loaded_at'] ?? 0);
        $loadedAt  = $sessionTs > 0 ? $sessionTs : $postTs;

        if ($loadedAt > 0 && $loadedAt <= time() + 60) {
            $elapsed = time() - $loadedAt;
            if ($elapsed < 1) {
                error_log('[QrPayment] Spam: too fast (' . $elapsed . 's)');
                return true;
            }
        }

        return false;
    }
}
