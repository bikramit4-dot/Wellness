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

        // Rate limiting: at most 5 real submissions per IP every 10 minutes,
        // so a bot can never flood the bookings inbox. Limited callers get the
        // same "thank you" so they can't tell they were throttled.
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        if (Security::throttle('appointment:' . $ip, self::RATE_MAX, self::RATE_WINDOW) > 0) {
            error_log('[Appointment] Rate limit hit for IP ' . $ip);
            $this->setFlash('success', 'Appointment request received. We will contact you shortly.');
            $this->redirect('/contact');
        }

        // Spam protection: honeypot field + minimum form-fill time. Bots are
        // silently "accepted" so they never learn they were caught.
        if ($this->looksLikeSpam()) {
            error_log('[Appointment] Spam submission blocked.');
            $this->setFlash('success', 'Appointment request received. We will contact you shortly.');
            $this->redirect('/contact');
        }

        // Length caps keep the stored data tidy and stop oversized payloads.
        $name = mb_substr(Security::sanitizeText($_POST['full_name'] ?? ''), 0, 100);
        $email = mb_substr(Security::sanitizeEmail($_POST['email'] ?? ''), 0, 190);
        $phone = mb_substr(Security::sanitizeText($_POST['phone'] ?? ''), 0, 30);
        $treatment = mb_substr(Security::sanitizeText($_POST['treatment'] ?? ''), 0, 120);
        $date = mb_substr(Security::sanitizeText($_POST['preferred_date'] ?? ''), 0, 10);
        $time = mb_substr(Security::sanitizeText($_POST['preferred_time'] ?? ''), 0, 10);
        $message = mb_substr(Security::sanitizeText($_POST['message'] ?? ''), 0, 2000);

        if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('danger', 'Please provide a valid name and email.');
            $this->redirect('/contact');
        }

        $model = new AppointmentModel();
        $model->save($name, $email, $phone, $treatment, $date, $time, $message);

        // Notify the admin by email. Mailer never throws; failures are logged internally.
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
            return true;
        }

        // 2. The server recorded when /contact was actually rendered — use that
        //    as the authoritative "page loaded" time (the hidden form field is
        //    only a hint; a bot can echo it back verbatim).
        $loadedAt = (int) ($_SESSION['contact_form_ts'] ?? 0);
        if ($loadedAt <= 0) {
            $loadedAt = (int) ($_POST['form_loaded_at'] ?? 0);
        }
        if ($loadedAt <= 0) {
            return true;
        }

        // 2b. A timestamp in the future means the request was forged.
        if ($loadedAt > time() + 60) {
            return true;
        }

        // 3. Humans take at least a few seconds to fill the form;
        //    automated submissions usually arrive almost instantly.
        return time() - $loadedAt < 3;
    }
}
