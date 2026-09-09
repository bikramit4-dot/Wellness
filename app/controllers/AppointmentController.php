<?php
class AppointmentController extends Controller
{
    /** Max submissions per IP inside the sliding window below. */
    private const RATE_MAX = 5;
    private const RATE_WINDOW = 600; // 10 minutes

    public function store(): void
    {
        if (!isset($_POST['csrf_token']) || !Security::validateCsrf($_POST['csrf_token'])) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/contact');
        }

        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        error_log('[Appointment] Store called. IP=' . $ip . ' SESSION_FORM_TS=' . ($_SESSION['contact_form_ts'] ?? 'NOT SET'));

        // Rate limiting
        if (Security::throttle('appointment:' . $ip, self::RATE_MAX, self::RATE_WINDOW) > 0) {
            error_log('[Appointment] RATE LIMIT hit for IP ' . $ip);
            $this->setFlash('success', 'Appointment request received. We will contact you shortly.');
            $this->redirect('/contact');
        }

        // Spam protection
        if ($this->looksLikeSpam()) {
            error_log('[Appointment] SPAM BLOCKED. IP=' . $ip);
            $this->setFlash('success', 'Appointment request received. We will contact you shortly.');
            $this->redirect('/contact');
        }

        error_log('[Appointment] Passed spam check. Processing save...');

        // Length caps keep the stored data tidy and stop oversized payloads.
        $name = mb_substr(Security::sanitizeText($_POST['full_name'] ?? ''), 0, 100);
        $email = mb_substr(Security::sanitizeEmail($_POST['email'] ?? ''), 0, 190);
        $phone = mb_substr(Security::sanitizeText($_POST['phone'] ?? ''), 0, 30);
        $treatment = mb_substr(Security::sanitizeText($_POST['treatment'] ?? ''), 0, 120);
        $date = mb_substr(Security::sanitizeText($_POST['preferred_date'] ?? ''), 0, 10);
        $time = mb_substr(Security::sanitizeText($_POST['preferred_time'] ?? ''), 0, 10);
        $message = mb_substr(Security::sanitizeText($_POST['message'] ?? ''), 0, 2000);

        // ── Validation ──────────────────────────────────────────────
        $errors = [];

        if ($name === '' || mb_strlen($name) < 2) {
            $errors[] = 'Full name is required (at least 2 characters).';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }
        if ($phone === '' || !preg_match('/^[+\d\s\-]{7,20}$/', $phone)) {
            $errors[] = 'A valid phone number is required (7-20 digits, may include +, spaces, dashes).';
        }
        $validTreatments = ['Naturopathy', 'Yoga Therapy', 'Acupuncture', 'Physiotherapy', 'Diet Therapy', 'Special Therapy'];
        if ($treatment === '' || !in_array($treatment, $validTreatments, true)) {
            $errors[] = 'Please select a valid treatment.';
        }
        if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $errors[] = 'Please select a preferred date.';
        } else {
            $dateObj = date_create($date);
            if ($dateObj === false || $dateObj < new DateTime('today')) {
                $errors[] = 'Preferred date cannot be in the past.';
            }
        }
        if ($time === '' || !preg_match('/^\d{2}:\d{2}$/', $time)) {
            $errors[] = 'Please select a preferred time.';
        }
        if ($message === '' || mb_strlen(trim($message)) < 10) {
            $errors[] = 'Please enter a message (at least 10 characters).';
        }

        if ($errors !== []) {
            $this->setFlash('danger', implode(' ', $errors));
            $this->redirect('/contact');
        }

        // ── Save ────────────────────────────────────────────────────
        $model = new AppointmentModel();
        $model->save($name, $email, $phone, $treatment, $date, $time, $message);

        error_log('[Appointment] SAVED: ' . $name . ' (' . $email . ') for ' . $treatment . ' on ' . $date . ' ' . $time);

        // In-app notification for the admin panel
        NotificationModel::add(
            'appointment',
            'New Appointment',
            $name . ' booked ' . $treatment . ' for ' . $date . ' at ' . $time,
            '/admin/appointments'
        );

        // Email notification to admin (non-blocking — failures are logged internally)
        Mailer::sendAppointmentNotification([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'treatment' => $treatment,
            'date' => $date,
            'time' => $time,
            'message' => $message,
        ]);

        $this->setFlash('success', 'Appointment request received. We will contact you shortly.');
        $this->redirect('/contact');
    }

    /**
     * Heuristic spam detection for the appointment form.
     */
    private function looksLikeSpam(): bool
    {
        // 1. Honeypot: real users never see or fill this field.
        if (trim((string) ($_POST['website'] ?? '')) !== '') {
            error_log('[Appointment] Spam: honeypot filled');
            return true;
        }

        // 2. Minimum form-fill time — use both session and POST timestamps.
        $sessionTs = (int) ($_SESSION['contact_form_ts'] ?? 0);
        $postTs    = (int) ($_POST['form_loaded_at'] ?? 0);
        $loadedAt  = $sessionTs > 0 ? $sessionTs : $postTs;

        if ($loadedAt > 0 && $loadedAt <= time() + 60) {
            $elapsed = time() - $loadedAt;
            if ($elapsed < 1) {
                error_log('[Appointment] Spam: too fast (' . $elapsed . 's)');
                return true;
            }
        }
        // If no timestamp available, allow through — honeypot is sufficient.

        return false;
    }
}
