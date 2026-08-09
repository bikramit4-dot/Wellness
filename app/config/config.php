<?php
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__, 2));
}

$baseDir = getenv('BASE_URL');
if ($baseDir === false) {
    if (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') {
        // CLI scripts (seed.php, migrations) have no web SCRIPT_NAME. Use the
        // project folder name so stored BASE_URL paths match the web URL
        // (e.g. /wellness) instead of the CLI script's own directory.
        $baseDir = '/' . basename(APP_ROOT);
    } else {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $baseDir = rtrim(dirname($scriptName), '/');
        $baseDir = $baseDir === '.' || $baseDir === '' ? '' : $baseDir;
    }
}

if (!defined('BASE_URL')) {
    define('BASE_URL', $baseDir);
}

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Harmony Wellness Center');
}

if (!defined('SITE_EMAIL')) {
    define('SITE_EMAIL', 'info@harmonywellness.com');
}

// ============ Admin panel credentials ============
// The admin area is available at: {BASE_URL}/admin
//   Username: ADMIN_USERNAME
//   Password: the value matching ADMIN_PASSWORD_HASH below.
//
// To rotate the password, run:
//   php app/scripts/admin-password.php "YourNewPassword"
// and paste the printed ADMIN_PASSWORD_HASH line into this file.
if (!defined('ADMIN_USERNAME')) {
    define('ADMIN_USERNAME', 'admin');
}
if (!defined('ADMIN_PASSWORD_HASH')) {
    // bcrypt hash of: Admin@Harmony2026
    define('ADMIN_PASSWORD_HASH', '$2y$12$nqIh1GJFupDA0tWL0VZe9.sMATwOyJPgp5ldhrEktvuhW4yW/8Zy6');
}

// ============ Admin auto-lock ============
// ADMIN_IDLE_LOCK_SECONDS: after this many seconds without mouse/keyboard
// activity, the admin panel shows a lock screen that requires the password
// to resume.
if (!defined('ADMIN_IDLE_LOCK_SECONDS')) {
    define('ADMIN_IDLE_LOCK_SECONDS', 20);
}
// ADMIN_SESSION_TIMEOUT: hard server-side session expiry (seconds). Even if
// the client-side lock is bypassed, the session dies after this long.
if (!defined('ADMIN_SESSION_TIMEOUT')) {
    define('ADMIN_SESSION_TIMEOUT', 1800); // 30 minutes
}
// ADMIN_LOCK_ON_ENTRY: when true, landing on the admin panel from outside
// (typing /admin, opening a new tab, or coming from the public site) shows
// the lock screen immediately, so a saved session never skips the password.
if (!defined('ADMIN_LOCK_ON_ENTRY')) {
    define('ADMIN_LOCK_ON_ENTRY', true);
}

// ============ Database (MySQL / MariaDB) ============
// XAMPP defaults: host 'localhost', user 'root', empty password.
// The schema is in database.sql — see also app/scripts/seed.php.
if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', getenv('DB_NAME') ?: 'wellness');
}
if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') ?: 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('DB_PASS') ?: '');
}

// ============ Email notifications (Resend) ============
// 1. Create an account + API key at https://resend.com (free tier: 3,000 emails/month)
// 2. Paste your API key below (or set the RESEND_API_KEY environment variable).
// 3. For testing you can send FROM 'onboarding@resend.dev' (goes to your account email).
//    For production, verify your own domain in Resend and use an address on it.
if (!defined('RESEND_API_KEY')) {
    define('RESEND_API_KEY', getenv('RESEND_API_KEY') ?: '');
}

if (!defined('MAILER_FROM')) {
    define('MAILER_FROM', 'onboarding@resend.dev'); // verified sender
}

if (!defined('MAILER_FROM_NAME')) {
    define('MAILER_FROM_NAME', SITE_NAME);
}

// Where new-appointment notifications are sent:
if (!defined('ADMIN_NOTIFY_EMAIL')) {
    define('ADMIN_NOTIFY_EMAIL', 'info@harmonywellness.com');
}

// ============ Auto cache-clear system ============
// When AUTO_CLEAR_CACHE is true (default), the site sends no-cache headers on
// every HTML response and versioned URLs for CSS/JS, so visitors always see the
// latest changes without manually clearing the browser cache. Set it to false
// in production if you prefer to let browsers cache aggressively.
if (!defined('AUTO_CLEAR_CACHE')) {
    define('AUTO_CLEAR_CACHE', true);
}

/**
 * Build a cache-busted URL for a static asset inside public/.
 *
 * Appends the file's last-modified time as a version query string (?v=...),
 * so whenever the file changes the URL changes too — browsers automatically
 * re-download it and the stale cache is never used.
 *
 * @param string $path path relative to public/, e.g. '/css/style.css'
 */
if (!function_exists('asset_url')) {
    function asset_url(string $path): string
    {
        $clean = '/' . ltrim($path, '/');
        $full = APP_ROOT . '/public' . $clean;
        $version = is_file($full) ? (string) @filemtime($full) : '';

        return BASE_URL . '/public' . $clean . ($version !== '' ? '?v=' . $version : '');
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
