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
        // curl_close() is a no-op since PHP 8.0 — omitted to avoid deprecation warning.

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
            . '<p style="margin:6px 0 0;color:#cfe3d8;font-size:13px;">Chitrawan Nature Cure Hospital</p>'
            . '</div>'
            . '<div style="background:#ffffff;border:1px solid #e4e9e4;border-top:none;border-radius:0 0 16px 16px;padding:28px;">'
            . '<p style="margin:0 0 18px;font-size:14px;color:#22302b;">A new appointment request has been received:</p>'
            . '<table style="width:100%;border-collapse:collapse;font-size:14px;color:#22302b;">' . $rowsHtml . '</table>'
            . '<p style="margin:22px 0 0;font-size:14px;color:#22302b;">Manage this booking in the admin panel:</p>'
            . '<a href="' . $adminUrl . '" style="display:inline-block;margin-top:6px;padding:12px 22px;background:#f4a261;color:#3a2a12;text-decoration:none;border-radius:999px;font-size:14px;font-weight:bold;">Open Admin Panel</a>'
            . '</div>'
            . '<p style="text-align:center;font-size:12px;color:#5f6f68;margin:14px 0 0;">This is an automated notification from the Chitrawan Nature Cure Hospital website.</p>'
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

    /**
     * Notify the admin when a new QR advance payment is submitted.
     *
     * @param array<string, string> $payment
     */
    public static function sendQrPaymentNotification(array $payment): bool
    {
        $to = defined('ADMIN_NOTIFY_EMAIL') ? trim(ADMIN_NOTIFY_EMAIL) : '';
        if ($to === '') {
            error_log('[Mailer] ADMIN_NOTIFY_EMAIL is not configured — email not sent.');
            return false;
        }

        $name = Security::e($payment['name'] ?? '');
        $email = Security::e($payment['email'] ?? '');
        $phone = Security::e($payment['phone'] ?? '');
        $address = Security::e($payment['address'] ?? '');
        $package = Security::e($payment['package'] ?? '');
        $amount = Security::e($payment['amount'] ?? '');
        $txn = Security::e($payment['transaction_id'] ?? '');
        $message = Security::e($payment['message'] ?? '');
        $screenshot = Security::e($payment['screenshot'] ?? '');
        $adminUrl = BASE_URL . '/admin/qr-payments';

        $subject = 'New Advance Payment — ' . ($payment['name'] ?? 'Customer');

        $row = static function (string $label, string $value): string {
            $value = $value !== '' ? $value : '—';
            return '<tr>'
                . '<td style="padding:10px 14px;background:#f1f6f3;border:1px solid #e4e9e4;font-weight:bold;width:36%;vertical-align:top;">' . $label . '</td>'
                . '<td style="padding:10px 14px;border:1px solid #e4e9e4;vertical-align:top;">' . $value . '</td>'
                . '</tr>';
        };

        $rowsHtml =
            $row('Full Name', $name)
            . $row('Email', $email !== '—' ? '<a href="mailto:' . $email . '" style="color:#2f7d5d;">' . $email . '</a>' : '—')
            . $row('Phone', $phone)
            . $row('Address', $address)
            . $row('Package', $package)
            . $row('Amount Paid', $amount)
            . $row('Transaction / UTR', $txn)
            . $row('Message', $message)
            . $row('Payment Screenshot', $screenshot !== '—'
                ? '<a href="' . $screenshot . '" style="color:#2f7d5d;">View screenshot</a>'
                : '—');

        $html = '<div style="background:#f1f6f3;padding:28px 16px;font-family:Arial,Helvetica,sans-serif;">'
            . '<div style="max-width:600px;margin:0 auto;">'
            . '<div style="background:linear-gradient(135deg,#16422f,#2f7d5d);border-radius:16px 16px 0 0;padding:24px 28px;">'
            . '<h1 style="margin:0;color:#ffffff;font-size:22px;font-family:Georgia,serif;">New Advance Payment</h1>'
            . '<p style="margin:6px 0 0;color:#cfe3d8;font-size:13px;">Chitrawan Nature Cure Hospital</p>'
            . '</div>'
            . '<div style="background:#ffffff;border:1px solid #e4e9e4;border-top:none;border-radius:0 0 16px 16px;padding:28px;">'
            . '<p style="margin:0 0 18px;font-size:14px;color:#22302b;">A customer submitted an advance payment through the tariff page:</p>'
            . '<table style="width:100%;border-collapse:collapse;font-size:14px;color:#22302b;">' . $rowsHtml . '</table>'
            . '<p style="margin:22px 0 0;font-size:14px;color:#22302b;">Verify the screenshot and update the status in the admin panel:</p>'
            . '<a href="' . $adminUrl . '" style="display:inline-block;margin-top:6px;padding:12px 22px;background:#f4a261;color:#3a2a12;text-decoration:none;border-radius:999px;font-size:14px;font-weight:bold;">Review Advance Payments</a>'
            . '</div>'
            . '<p style="text-align:center;font-size:12px;color:#5f6f68;margin:14px 0 0;">This is an automated notification from the Chitrawan Nature Cure Hospital website.</p>'
            . '</div>'
            . '</div>';

        $text = 'New advance payment received:' . PHP_EOL . PHP_EOL
            . 'Full Name: ' . ($payment['name'] ?? '') . PHP_EOL
            . 'Email: ' . ($payment['email'] ?? '') . PHP_EOL
            . 'Phone: ' . ($payment['phone'] ?? '') . PHP_EOL
            . 'Address: ' . ($payment['address'] ?? '') . PHP_EOL
            . 'Package: ' . ($payment['package'] ?? '') . PHP_EOL
            . 'Amount Paid: ' . ($payment['amount'] ?? '') . PHP_EOL
            . 'Transaction / UTR: ' . ($payment['transaction_id'] ?? '') . PHP_EOL
            . 'Message: ' . ($payment['message'] ?? '') . PHP_EOL
            . 'Screenshot: ' . ($payment['screenshot'] ?? '') . PHP_EOL . PHP_EOL
            . 'Review in the admin panel: ' . $adminUrl;

        return self::send([$to], $subject, $html, $text);
    }

    /* ------------------------------------------------------------------ */
    /* Customer-facing emails (sent when admin changes status)            */
    /* ------------------------------------------------------------------ */

    /**
     * Notify the customer when their QR advance payment is verified or rejected.
     */
    public static function sendPaymentStatusEmail(array $payment, string $status): bool
    {
        $email = trim((string) ($payment['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log('[Mailer] No valid customer email for payment status notification.');
            return false;
        }

        $name = Security::e($payment['name'] ?? '');
        $package = Security::e($payment['package'] ?? '');
        $amount = Security::e($payment['amount'] ?? '');
        $txn = Security::e($payment['transaction_id'] ?? '');

        if ($status === 'verified') {
            $subject = 'Payment Verified — Thank you, ' . ($payment['name'] ?? 'Customer');
            $headline = 'Payment Verified ✓';
            $message = 'Your advance payment has been verified by our team. Your booking is now confirmed!';
            $details = 'Package: ' . $package . '<br>Amount: ' . $amount . '<br>Transaction: ' . $txn;
            $ctaLabel = 'View Your Booking';
            $ctaColor = '#22c55e';
        } else {
            $subject = 'Payment Update — ' . ($payment['name'] ?? 'Customer');
            $headline = 'Payment Not Verified';
            $message = 'We were unable to verify your advance payment. Please check the details and try again, or contact us for help.';
            $details = 'Package: ' . $package . '<br>Amount: ' . $amount . '<br>Transaction: ' . $txn;
            $ctaLabel = 'Contact Us';
            $ctaColor = '#dc2626';
        }

        $ctaUrl = BASE_URL . '/contact';

        $html = '<div style="background:#f1f6f3;padding:28px 16px;font-family:Arial,Helvetica,sans-serif;">'
            . '<div style="max-width:600px;margin:0 auto;">'
            . '<div style="background:linear-gradient(135deg,#16422f,#2f7d5d);border-radius:16px 16px 0 0;padding:24px 28px;">'
            . '<h1 style="margin:0;color:#ffffff;font-size:22px;font-family:Georgia,serif;">' . $headline . '</h1>'
            . '<p style="margin:6px 0 0;color:#cfe3d8;font-size:13px;">Chitrawan Nature Cure Hospital</p>'
            . '</div>'
            . '<div style="background:#ffffff;border:1px solid #e4e9e4;border-top:none;border-radius:0 0 16px 16px;padding:28px;">'
            . '<p style="margin:0 0 18px;font-size:14px;color:#22302b;">Dear ' . $name . ',</p>'
            . '<p style="margin:0 0 18px;font-size:14px;color:#22302b;">' . $message . '</p>'
            . '<table style="width:100%;border-collapse:collapse;font-size:14px;color:#22302b;margin-bottom:20px;">'
            . '<tr><td style="padding:10px 14px;background:#f1f6f3;border:1px solid #e4e9e4;font-weight:bold;width:36%;">Details</td>'
            . '<td style="padding:10px 14px;border:1px solid #e4e9e4;">' . $details . '</td></tr>'
            . '</table>'
            . '<a href="' . $ctaUrl . '" style="display:inline-block;padding:12px 22px;background:' . $ctaColor . ';color:#ffffff;text-decoration:none;border-radius:999px;font-size:14px;font-weight:bold;">' . $ctaLabel . '</a>'
            . '</div>'
            . '<p style="text-align:center;font-size:12px;color:#5f6f68;margin:14px 0 0;">If you have questions, reply to this email or call us. — Chitrawan Nature Cure Hospital</p>'
            . '</div>'
            . '</div>';

        $text = $headline . PHP_EOL . PHP_EOL
            . 'Dear ' . $name . ',' . PHP_EOL . PHP_EOL
            . strip_tags($message) . PHP_EOL . PHP_EOL
            . 'Package: ' . ($payment['package'] ?? '') . PHP_EOL
            . 'Amount: ' . ($payment['amount'] ?? '') . PHP_EOL
            . 'Transaction: ' . ($payment['transaction_id'] ?? '') . PHP_EOL . PHP_EOL
            . $ctaUrl;

        return self::send([$email], $subject, $html, $text);
    }

    /**
     * Notify the customer when their appointment status changes.
     */
    public static function sendAppointmentStatusEmail(array $appointment, string $status): bool
    {
        $email = trim((string) ($appointment['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log('[Mailer] No valid customer email for appointment status notification.');
            return false;
        }

        $name = Security::e($appointment['name'] ?? '');
        $treatment = Security::e($appointment['treatment'] ?? '');
        $date = Security::e($appointment['date'] ?? '');
        $time = Security::e($appointment['time'] ?? '');

        if ($status === 'confirmed') {
            $subject = 'Appointment Confirmed — ' . ($appointment['name'] ?? 'Patient');
            $headline = 'Appointment Confirmed ✓';
            $message = 'Great news! Your appointment has been confirmed by our team.';
            $ctaLabel = 'View Details';
            $ctaColor = '#22c55e';
        } elseif ($status === 'rejected') {
            $subject = 'Appointment Update — ' . ($appointment['name'] ?? 'Patient');
            $headline = 'Appointment Request Update';
            $message = 'We are sorry, but we cannot confirm this appointment request. Please contact us to choose another date or time.';
            $ctaLabel = 'Contact Us';
            $ctaColor = '#dc2626';
        } elseif ($status === 'completed') {
            $subject = 'Thank You for Your Visit — ' . ($appointment['name'] ?? 'Patient');
            $headline = 'Visit Completed';
            $message = 'Thank you for visiting Chitrawan Nature Cure Hospital. We hope your session was helpful!';
            $ctaLabel = 'Book Another Session';
            $ctaColor = '#2f7d5d';
        } else {
            return false; // Don't email for other statuses
        }

        $ctaUrl = BASE_URL . '/contact';

        $html = '<div style="background:#f1f6f3;padding:28px 16px;font-family:Arial,Helvetica,sans-serif;">'
            . '<div style="max-width:600px;margin:0 auto;">'
            . '<div style="background:linear-gradient(135deg,#16422f,#2f7d5d);border-radius:16px 16px 0 0;padding:24px 28px;">'
            . '<h1 style="margin:0;color:#ffffff;font-size:22px;font-family:Georgia,serif;">' . $headline . '</h1>'
            . '<p style="margin:6px 0 0;color:#cfe3d8;font-size:13px;">Chitrawan Nature Cure Hospital</p>'
            . '</div>'
            . '<div style="background:#ffffff;border:1px solid #e4e9e4;border-top:none;border-radius:0 0 16px 16px;padding:28px;">'
            . '<p style="margin:0 0 18px;font-size:14px;color:#22302b;">Dear ' . $name . ',</p>'
            . '<p style="margin:0 0 18px;font-size:14px;color:#22302b;">' . $message . '</p>'
            . '<table style="width:100%;border-collapse:collapse;font-size:14px;color:#22302b;margin-bottom:20px;">'
            . '<tr><td style="padding:10px 14px;background:#f1f6f3;border:1px solid #e4e9e4;font-weight:bold;width:36%;">Treatment</td>'
            . '<td style="padding:10px 14px;border:1px solid #e4e9e4;">' . $treatment . '</td></tr>'
            . '<tr><td style="padding:10px 14px;background:#f1f6f3;border:1px solid #e4e9e4;font-weight:bold;">Date</td>'
            . '<td style="padding:10px 14px;border:1px solid #e4e9e4;">' . $date . '</td></tr>'
            . '<tr><td style="padding:10px 14px;background:#f1f6f3;border:1px solid #e4e9e4;font-weight:bold;">Time</td>'
            . '<td style="padding:10px 14px;border:1px solid #e4e9e4;">' . $time . '</td></tr>'
            . '</table>'
            . '<a href="' . $ctaUrl . '" style="display:inline-block;padding:12px 22px;background:' . $ctaColor . ';color:#ffffff;text-decoration:none;border-radius:999px;font-size:14px;font-weight:bold;">' . $ctaLabel . '</a>'
            . '</div>'
            . '<p style="text-align:center;font-size:12px;color:#5f6f68;margin:14px 0 0;">If you have questions, reply to this email or call us. — Chitrawan Nature Cure Hospital</p>'
            . '</div>'
            . '</div>';

        $text = $headline . PHP_EOL . PHP_EOL
            . 'Dear ' . $name . ',' . PHP_EOL . PHP_EOL
            . strip_tags($message) . PHP_EOL . PHP_EOL
            . 'Treatment: ' . ($appointment['treatment'] ?? '') . PHP_EOL
            . 'Date: ' . ($appointment['date'] ?? '') . PHP_EOL
            . 'Time: ' . ($appointment['time'] ?? '') . PHP_EOL . PHP_EOL
            . $ctaUrl;

        return self::send([$email], $subject, $html, $text);
    }
}
