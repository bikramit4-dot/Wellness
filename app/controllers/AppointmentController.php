<?php
class AppointmentController extends Controller
{
    public function store(): void
    {
        if (!isset($_POST['csrf_token']) || !Security::validateCsrf($_POST['csrf_token'])) {
            $this->setFlash('danger', 'Invalid security token.');
            $this->redirect('/contact');
        }

        // Spam protection: honeypot field + minimum form-fill time. Bots are
        // silently "accepted" so they never learn they were caught.
        if ($this->looksLikeSpam()) {
            error_log('[Appointment] Spam submission blocked.');
            $this->setFlash('success', 'Appointment request received. We will contact you shortly.');
            $this->redirect('/contact');
        }

        $name = Security::sanitizeText($_POST['full_name'] ?? '');
        $email = Security::sanitizeEmail($_POST['email'] ?? '');
        $phone = Security::sanitizeText($_POST['phone'] ?? '');
        $treatment = Security::sanitizeText($_POST['treatment'] ?? '');
        $date = Security::sanitizeText($_POST['preferred_date'] ?? '');
        $time = Security::sanitizeText($_POST['preferred_time'] ?? '');
        $message = Security::sanitizeText($_POST['message'] ?? '');

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

        // 2. The form renders a hidden timestamp. A missing value means the
        //    submission didn't come through the real page.
        $loadedAt = (int) ($_POST['form_loaded_at'] ?? 0);
        if ($loadedAt <= 0) {
            return true;
        }

        // 3. Humans take at least a few seconds to fill the form;
        //    automated submissions usually arrive almost instantly.
        return time() - $loadedAt < 3;
    }
}
