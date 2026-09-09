<?php
/**
 * Handles public review/testimonial submissions.
 *
 * Customers submit reviews via a form on the home page.
 * Reviews are saved as 'pending' and become visible only after
 * admin approval from the admin panel.
 */
class ReviewController extends Controller
{
    /** Max submissions per IP inside the sliding window. */
    private const RATE_MAX = 3;
    private const RATE_WINDOW = 3600; // 1 hour

    public function store(): void
    {
        if (!isset($_POST['csrf_token']) || !Security::validateCsrf($_POST['csrf_token'])) {
            $this->setFlash('danger', 'Invalid security token. Please try again.');
            $this->redirect('/#reviews');
        }

        // Rate limiting
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        if (Security::throttle('review:' . $ip, self::RATE_MAX, self::RATE_WINDOW) > 0) {
            $this->setFlash('success', 'Thank you! Your review has been submitted and will appear after approval.');
            $this->redirect('/#reviews');
        }

        // Spam protection
        if ($this->looksLikeSpam()) {
            $this->setFlash('success', 'Thank you! Your review has been submitted and will appear after approval.');
            $this->redirect('/#reviews');
        }

        // Sanitize inputs
        $name = mb_substr(Security::sanitizeText($_POST['review_name'] ?? ''), 0, 120);
        $role = mb_substr(Security::sanitizeText($_POST['review_role'] ?? ''), 0, 120);
        $rating = (int) ($_POST['review_rating'] ?? 5);
        $quote = mb_substr(Security::sanitizeText($_POST['review_quote'] ?? ''), 0, 2000);

        // Validation
        $errors = [];

        if ($name === '' || mb_strlen($name) < 2) {
            $errors[] = 'Name is required (at least 2 characters).';
        }
        if ($rating < 1 || $rating > 5) {
            $rating = 5; // Default to 5 if invalid
        }
        if ($quote === '' || mb_strlen(trim($quote)) < 10) {
            $errors[] = 'Please write at least 10 characters in your review.';
        }

        if ($errors !== []) {
            $this->setFlash('danger', implode(' ', $errors));
            $this->redirect('/#reviews');
        }

        // Generate initials from name
        $parts = explode(' ', $name);
        $initials = '';
        foreach ($parts as $part) {
            $initials .= mb_strtoupper(mb_substr(trim($part), 0, 1));
        }
        $initials = mb_substr($initials, 0, 2);

        // Pick a random avatar color
        $avatars = ['a1', 'a2', 'a3', 'a4', 'a5', 'a6'];
        $avatar = $avatars[array_rand($avatars)];

        // Save as pending
        $model = new TestimonialModel();
        $model->save([
            'name' => $name,
            'role' => $role,
            'initials' => $initials,
            'avatar' => $avatar,
            'rating' => $rating,
            'quote' => $quote,
            'status' => 'pending',
            'sort_order' => 0,
        ]);

        // Notify admin
        NotificationModel::add(
            'review',
            'New Review Submitted',
            $name . ' submitted a ' . $rating . '-star review: "' . mb_substr($quote, 0, 80) . '..."',
            '/admin/testimonials'
        );

        $this->setFlash('success', 'Thank you for your review! It will be visible on our website after admin approval.');
        $this->redirect('/#reviews');
    }

    /**
     * Heuristic spam detection.
     */
    private function looksLikeSpam(): bool
    {
        // Honeypot
        if (trim((string) ($_POST['website'] ?? '')) !== '') {
            error_log('[Review] Spam: honeypot filled');
            return true;
        }

        // Minimum form-fill time
        $sessionTs = (int) ($_SESSION['review_form_ts'] ?? 0);
        $postTs = (int) ($_POST['form_loaded_at'] ?? 0);
        $loadedAt = $sessionTs > 0 ? $sessionTs : $postTs;

        if ($loadedAt > 0 && $loadedAt <= time() + 60) {
            $elapsed = time() - $loadedAt;
            if ($elapsed < 2) {
                error_log('[Review] Spam: too fast (' . $elapsed . 's)');
                return true;
            }
        }

        return false;
    }
}
