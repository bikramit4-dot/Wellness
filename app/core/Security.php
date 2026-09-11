<?php
class Security
{
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeText(mixed $value): string
    {
        return trim(strip_tags((string) $value));
    }

    public static function sanitizeEmail(mixed $value): string
    {
        return filter_var(trim((string) $value), FILTER_SANITIZE_EMAIL);
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function validateCsrf(string $token): bool
    {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    /**
     * Sliding-window rate limiter backed by a small JSON file in storage/.
     *
     * @param string $key          bucket name, e.g. 'appointment:1.2.3.4'
     * @param int    $max          max allowed hits inside the window
     * @param int    $windowSeconds window length in seconds
     * @param string $file         storage file (defaults to storage/throttle.json)
     * @return int 0 when allowed, otherwise seconds to wait before retrying
     */
    public static function throttle(string $key, int $max, int $windowSeconds, string $file = ''): int
    {
        $file = $file !== '' ? $file : APP_ROOT . '/storage/throttle.json';

        $data = [];
        if (is_file($file)) {
            $decoded = json_decode((string) @file_get_contents($file), true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        $now = time();
        $hits = array_values(array_filter(
            (array) ($data[$key] ?? []),
            static fn ($t): bool => is_int($t) && $t > $now - $windowSeconds
        ));

        if (count($hits) >= $max) {
            // Bucket is full: keep the latest hits, tell caller when it frees up.
            $data[$key] = array_slice($hits, -$max);
            @file_put_contents($file, json_encode($data), LOCK_EX);

            return max(1, $hits[0] + $windowSeconds - $now);
        }

        $hits[] = $now;
        $data[$key] = array_slice($hits, -$max);

        // Forget buckets that have been quiet for over a day.
        foreach ($data as $k => $times) {
            if (empty($times) || max($times) < $now - 86400) {
                unset($data[$k]);
            }
        }

        @file_put_contents($file, json_encode($data), LOCK_EX);
        return 0;
    }
}