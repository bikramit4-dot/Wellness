<?php
/**
 * Google OAuth 2.0 helper for admin login.
 *
 * Uses pure PHP (curl) — no Composer packages required.
 * Flow:
 *   1. Admin clicks "Sign in with Google" → redirected to Google.
 *   2. Google redirects back with ?code=… → we exchange for tokens + user info.
 *   3. If the email is in GOOGLE_ALLOWED_EMAILS, log them in.
 */
class GoogleOAuth
{
    private const AUTH_ENDPOINT  = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';
    private const USERINFO_ENDPOINT = 'https://www.googleapis.com/oauth2/v2/userinfo';

    /**
     * Build the Google authorization URL and redirect the user.
     */
    public static function redirect(): void
    {
        $clientId     = self::clientId();
        $redirectUri  = self::redirectUri();
        $state        = bin2hex(random_bytes(16));

        $_SESSION['google_oauth_state'] = $state;

        $params = http_build_query([
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'online',
            'prompt'        => 'select_account',
            'state'         => $state,
        ]);

        header('Location: ' . self::AUTH_ENDPOINT . '?' . $params);
        exit;
    }

    /**
     * Handle the callback from Google.
     *
     * @return array{ok: bool, email?: string, name?: string, picture?: string, error?: string}
     */
    public static function handleCallback(): array
    {
        // 1. Validate state
        $state = $_GET['state'] ?? '';
        if (!hash_equals($_SESSION['google_oauth_state'] ?? '', $state)) {
            return ['ok' => false, 'error' => 'Invalid state parameter. Please try again.'];
        }
        unset($_SESSION['google_oauth_state']);

        // 2. Check for errors from Google
        if (isset($_GET['error'])) {
            return ['ok' => false, 'error' => 'Google denied the request: ' . ($_GET['error'] ?? 'unknown')];
        }

        $code = $_GET['code'] ?? '';
        if ($code === '') {
            return ['ok' => false, 'error' => 'No authorization code received.'];
        }

        // 3. Exchange authorization code for tokens
        $tokenData = self::exchangeCode($code);
        if (!isset($tokenData['access_token'])) {
            return ['ok' => false, 'error' => 'Failed to exchange authorization code: ' . ($tokenData['error_description'] ?? 'unknown error')];
        }

        // 4. Fetch user info from Google
        $userInfo = self::fetchUserInfo($tokenData['access_token']);
        if (!isset($userInfo['email'])) {
            return ['ok' => false, 'error' => 'Failed to retrieve user information from Google.'];
        }

        // 5. Check if this email is allowed
        $allowedEmails = self::allowedEmails();
        if (!empty($allowedEmails) && !in_array(strtolower($userInfo['email']), $allowedEmails, true)) {
            return ['ok' => false, 'error' => 'This Google account (' . Security::e($userInfo['email']) . ') is not authorized for admin access.'];
        }

        return [
            'ok'      => true,
            'email'   => $userInfo['email'],
            'name'    => $userInfo['name'] ?? '',
            'picture' => $userInfo['picture'] ?? '',
        ];
    }

    /**
     * Exchange the authorization code for access + ID tokens.
     *
     * @return array<string, mixed>
     */
    private static function exchangeCode(string $code): array
    {
        $payload = http_build_query([
            'client_id'     => self::clientId(),
            'client_secret' => self::clientSecret(),
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => self::redirectUri(),
        ]);

        return self::post(self::TOKEN_ENDPOINT, $payload);
    }

    /**
     * Fetch the authenticated user's profile from Google.
     *
     * @return array<string, mixed>
     */
    private static function fetchUserInfo(string $accessToken): array
    {
        $ch = curl_init(self::USERINFO_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
            CURLOPT_TIMEOUT        => 10,
        ]);
        $body = curl_exec($ch);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === false || $err !== '') {
            error_log('[GoogleOAuth] userinfo request failed: ' . $err);

            return [];
        }

        $data = json_decode($body, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Simple POST helper (curl).
     *
     * @return array<string, mixed>
     */
    private static function post(string $url, string $body): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 10,
        ]);
        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($response === false || $err !== '') {
            error_log('[GoogleOAuth] token exchange failed: ' . $err);

            return ['error_description' => $err ?: 'Network error'];
        }

        $data = json_decode($response, true);

        return is_array($data) ? $data : [];
    }

    /* ---------- Config helpers ---------- */

    public static function clientId(): string
    {
        return getenv('GOOGLE_CLIENT_ID') ?: '';
    }

    public static function clientSecret(): string
    {
        return getenv('GOOGLE_CLIENT_SECRET') ?: '';
    }

    /**
     * The callback URL Google redirects back to after authentication.
     */
    public static function redirectUri(): string
    {
        $base = defined('BASE_URL') ? (string) BASE_URL : '';
        $host = ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost');
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        return $scheme . '://' . $host . $base . '/admin/google-callback';
    }

    /**
     * Comma-separated list of allowed Google email addresses.
     * Empty array = anyone with a Google account can log in (not recommended).
     *
     * @return list<string>
     */
    public static function allowedEmails(): array
    {
        $raw = getenv('GOOGLE_ALLOWED_EMAILS') ?: '';
        if ($raw === '') {
            return [];
        }

        $emails = array_map('strtolower', array_map('trim', explode(',', $raw)));

        return array_filter($emails, static fn(string $e): bool => $e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL));
    }

    /**
     * Check if Google OAuth is configured (client ID + secret are set).
     */
    public static function isConfigured(): bool
    {
        return self::clientId() !== '' && self::clientSecret() !== '';
    }
}
