<?php
/**
 * Sends emails through the Resend REST API using cURL (no packages required).
 * See https://resend.com/docs for the API reference.
 */
class Mailer
{
    private const API_URL = 'https://api.resend.com/emails';

    public static function isConfigured(): bool
    {
        return defined('RESEND_API_KEY') && RESEND_API_KEY !== '';
    }

    /**
     * Send an email.
     *
     * @param array<int, string> $to Recipient email addresses.
     */
    public static function send(array $to, string $subject, string $html, ?string $text = null): bool
    {
        if (!self::isConfigured()) {
            error_log('[Mailer] RESEND_API_KEY is not configured — email not sent.');
            return false;
        }

        $from = defined('MAILER_FROM') ? trim(MAILER_FROM) : '';
        $fromName = defined('MAILER_FROM_NAME') ? trim(MAILER_FROM_NAME) : '';

        if ($from === '') {
            error_log('[Mailer] MAILER_FROM is not configured — email not sent.');
            return false;
        }

        $payload = [
            'from' => $fromName !== '' ? $fromName . ' <' . $from . '>' : $from,
            'to' => array_values($to),
            'subject' => $subject,
            'html' => $html,
        ];
        if ($text !== null) {
            $payload['text'] = $text;
        }

        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($payloadJson === false) {
            error_log('[Mailer] Failed to encode email payload (invalid UTF-8?).');
            return false;
        }

        $ch = curl_init(self::API_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payloadJson,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . RESEND_API_KEY,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlError !== '') {
            error_log('[Mailer] cURL error: ' . $curlError);
            return false;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log('[Mailer] Resend error (' . $httpCode . '): ' . substr((string) $response, 0, 500));
            return false;
        }

        error_log('[Mailer] Email sent OK to ' . implode(', ', $to));
        return true;
    }

    /**
     * Send the admin a notification when a new appointment is booked.
     *
     * @param array<string, string> $appointment
     */
    public static function sendAppointmentNotification(array $appointment): bool
    {
        $to = defined('ADMIN_NOTIFY_EMAIL') ? trim(ADMIN_NOTIFY_EMAIL) : '';
        if ($to === '') {
            error_log('[Mailer] ADMIN_NOTIFY_EMAIL is not configured — email not sent.');
            return false;
        }

        $name = Security::e($appointment['name'] ?? '');
        $email = Security::e($appointment['email'] ?? '');
        $phone = Security::e($appointment['phone'] ?? '');
        $treatment = Security::e($appointment['treatment'] ?? '');
        $date = Security::e($appointment['date'] ?? '');
        $time = Security::e($appointment['time'] ?? '');
        $message = Security::e($appointment['message'] ?? '');
        $adminUrl = BASE_URL . '/admin';

        $subject = 'New Appointment Booking — ' . ($appointment['name'] ?? 'New patient');

        $row = static function (string $label, string $value): string {
            $value = $value !== '' ? $value : '—';
            return '<tr>'
                . '<td style="padding:10px 14px;background:#f1f6f3;border:1px solid #e4e9e4;font-weight:bold;width:36%;vertical-align:top;">' . $label . '</td>'
                . '<td style="padding:10px 14px;border:1px solid #e4e9e4;vertical-align:top;">' . $value . '</td>'
                . '</tr>';
        };

        $rowsHtml =
            $row('Patient Name', $name)
            . $row('Email', $email !== '—' ? '<a href="mailto:' . $email . '" style="color:#2f7d5d;">' . $email . '</a>' : '—')
            . $row('Phone', $phone)
            . $row('Treatment', $treatment)
            . $row('Preferred Date', $date)
            . $row('Preferred Time', $time)
            . $row('Message', $message);

        $html = '<div style="background:#f1f6f3;padding:28px 16px;font-family:Arial,Helvetica,sans-serif;">'
            . '<div style="max-width:600px;margin:0 auto;">'
            . '<div style="background:linear-gradient(135deg,#16422f,#2f7d5d);border-radius:16px 16px 0 0;padding:24px 28px;">'
            . '<h1 style="margin:0;color:#ffffff;font-size:22px;font-family:Georgia,serif;">New Appointment Booking</h1>'
            . '<p style="margin:6px 0 0;color:#cfe3d8;font-size:13px;">Harmony Wellness Center</p>'
            . '</div>'
            . '<div style="background:#ffffff;border:1px solid #e4e9e4;border-top:none;border-radius:0 0 16px 16px;padding:28px;">'
            . '<p style="margin:0 0 18px;font-size:14px;color:#22302b;">A new appointment request has been received:</p>'
            . '<table style="width:100%;border-collapse:collapse;font-size:14px;color:#22302b;">' . $rowsHtml . '</table>'
            . '<p style="margin:22px 0 0;font-size:14px;color:#22302b;">Manage this booking in the admin panel:</p>'
            . '<a href="' . $adminUrl . '" style="display:inline-block;margin-top:6px;padding:12px 22px;background:#f4a261;color:#3a2a12;text-decoration:none;border-radius:999px;font-size:14px;font-weight:bold;">Open Admin Panel</a>'
            . '</div>'
            . '<p style="text-align:center;font-size:12px;color:#5f6f68;margin:14px 0 0;">This is an automated notification from the Harmony Wellness Center website.</p>'
            . '</div>'
            . '</div>';

        $text = 'New appointment request:' . PHP_EOL . PHP_EOL
            . 'Patient Name: ' . ($appointment['name'] ?? '') . PHP_EOL
            . 'Email: ' . ($appointment['email'] ?? '') . PHP_EOL
            . 'Phone: ' . ($appointment['phone'] ?? '') . PHP_EOL
            . 'Treatment: ' . ($appointment['treatment'] ?? '') . PHP_EOL
            . 'Preferred Date: ' . ($appointment['date'] ?? '') . PHP_EOL
            . 'Preferred Time: ' . ($appointment['time'] ?? '') . PHP_EOL
            . 'Message: ' . ($appointment['message'] ?? '') . PHP_EOL . PHP_EOL
            . 'Manage in the admin panel: ' . $adminUrl;

        return self::send([$to], $subject, $html, $text);
    }
}
