<?php
/**
 * QR advance payments submitted through the Tariff page.
 *
 * Stored in MySQL (`qr_payments` table). If the database is unavailable,
 * operations fall back to the JSON-lines file (storage/qr-payments.txt)
 * so a payment submission never fails.
 */
class QrPaymentModel
{
    private const STATUSES = ['new', 'verified', 'rejected'];

    /* ------------------------------------------------------------------ */
    /* Public API (same for DB and file backends)                         */
    /* ------------------------------------------------------------------ */    public function save(
        string $name,
        string $email,
        string $phone,
        string $address,
        string $package,
        string $amount,
        string $transactionId,
        string $message,
        string $screenshot
    ): void {
        $data = [
            'id' => bin2hex(random_bytes(8)),
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'package' => $package,
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'message' => $message,
            'screenshot' => $screenshot,
            'status' => 'new',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO qr_payments (id, name, email, phone, address, package, amount, transaction_id, message, screenshot, status, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $data['id'], $data['name'], $data['email'], $data['phone'],
                    $data['address'], $data['package'], $data['amount'], $data['transaction_id'], $data['message'], $data['screenshot'], $data['status'], $data['created_at'],
                ]);
                return;
            } catch (Throwable $e) {
                error_log('[QrPaymentModel] DB save failed, using file fallback: ' . $e->getMessage());
            }
        }

        $this->saveToFile($data);
    }

    /**
     * @return array<int, array<string, string>> newest first
     */
    public function all(): array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $rows = $pdo->query('SELECT * FROM qr_payments ORDER BY created_at DESC, id DESC')->fetchAll();
                $out = [];
                foreach ($rows as $r) {
                    $r['status'] = $r['status'] ?? 'new';
                    $out[] = $r;
                }

                return $out;
            } catch (Throwable $e) {
                error_log('[QrPaymentModel] DB read failed, using file fallback: ' . $e->getMessage());
            }
        }

        return $this->allFromFile();
    }

    public function delete(string $id): bool
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('DELETE FROM qr_payments WHERE id = ?');
                $stmt->execute([$id]);

                return $stmt->rowCount() > 0;
            } catch (Throwable $e) {
                error_log('[QrPaymentModel] DB delete failed, using file fallback: ' . $e->getMessage());
            }
        }

        return $this->deleteFromFile($id);
    }

    public function updateStatus(string $id, string $status): bool
    {
        if (!in_array($status, self::STATUSES, true)) {
            return false;
        }

        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('UPDATE qr_payments SET status = ? WHERE id = ?');
                $stmt->execute([$status, $id]);

                return $stmt->rowCount() > 0;
            } catch (Throwable $e) {
                error_log('[QrPaymentModel] DB update failed, using file fallback: ' . $e->getMessage());
            }
        }

        return $this->updateStatusFromFile($id, $status);
    }

    /* ------------------------------------------------------------------ */
    /* File fallback (storage/qr-payments.txt)                            */
    /* ------------------------------------------------------------------ */

    private function filePath(): string
    {
        return APP_ROOT . '/storage/qr-payments.txt';
    }

    /**
     * @param array<string, string> $data
     */
    private function saveToFile(array $data): void
    {
        $dir = dirname($this->filePath());
        if (!is_dir($dir)) {
            // 0777 so the web server user (e.g. XAMPP's 'daemon') can write to it.
            mkdir($dir, 0777, true);
        }

        $file = $this->filePath();
        if (!is_file($file)) {
            @touch($file);
        }
        @chmod($file, 0666);

        $line = json_encode($data, JSON_UNESCAPED_SLASHES) . PHP_EOL;
        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function allFromFile(): array
    {
        $parsed = [];
        foreach ($this->readLines() as $line) {
            $row = json_decode($line, true);
            if (is_array($row)) {
                $row['status'] = $row['status'] ?? 'new';
                $parsed[] = $row;
            }
        }

        usort($parsed, static fn (array $a, array $b): int =>
            strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));

        return $parsed;
    }

    private function deleteFromFile(string $id): bool
    {
        $rows = $this->readLines();
        $found = false;

        foreach ($rows as $i => $line) {
            $row = json_decode($line, true);
            if (is_array($row) && ($row['id'] ?? null) === $id) {
                unset($rows[$i]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            return false;
        }

        $this->writeLines($rows);
        return true;
    }

    private function updateStatusFromFile(string $id, string $status): bool
    {
        $rows = $this->readLines();

        foreach ($rows as $i => $line) {
            $row = json_decode($line, true);
            if (is_array($row) && ($row['id'] ?? null) === $id) {
                $row['status'] = $status;
                $rows[$i] = json_encode($row, JSON_UNESCAPED_SLASHES);
                $this->writeLines($rows);
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    private function readLines(): array
    {
        if (!is_file($this->filePath())) {
            return [];
        }

        return file($this->filePath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    }

    /**
     * @param array<int, string> $lines
     */
    private function writeLines(array $lines): void
    {
        $clean = array_values(array_filter($lines, static fn (string $l): bool => trim($l) !== ''));
        $file = $this->filePath();
        @chmod($file, 0666);
        file_put_contents($file, implode(PHP_EOL, $clean) . PHP_EOL, LOCK_EX);
    }
}
