<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin | <?= Security::e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body class="admin-body<?= !empty($_SESSION['admin_logged_in']) ? ' admin-authed' : '' ?>" data-base="<?= Security::e(BASE_URL) ?>" data-idle-lock="<?= Security::e((string) (defined('ADMIN_IDLE_LOCK_SECONDS') ? ADMIN_IDLE_LOCK_SECONDS : 20)) ?>" data-lock-entry="<?= Security::e((defined('ADMIN_LOCK_ON_ENTRY') && ADMIN_LOCK_ON_ENTRY) ? '1' : '0') ?>" data-csrf="<?= Security::e(Security::csrfToken()) ?>">

<!-- ============ SVG Icon Sprite ============ -->
<?php require APP_ROOT . '/app/views/partials/icons.php'; ?>

<header class="admin-topbar">
    <div class="container admin-topbar-inner">
        <a class="brand" href="<?= BASE_URL ?>/admin">
            <span class="brand-mark" aria-hidden="true"><svg class="icon"><use href="#icon-shield"/></svg></span>
            <span>Harmony <em>Admin</em></span>
        </a>
        <div class="admin-topbar-actions">
            <a class="admin-link" href="<?= BASE_URL ?>/admin">Dashboard</a>
            <a class="admin-link" href="<?= BASE_URL ?>/">View Site</a>
            <?php if (!empty($_SESSION['admin_logged_in'])): ?>
                <span class="admin-user">Signed in as <strong><?= Security::e($_SESSION['admin_display_name'] ?? ($_SESSION['admin_username'] ?? 'Admin')) ?></strong></span>
                <form action="<?= BASE_URL ?>/admin/logout" method="post">
                    <input type="hidden" name="csrf_token" value="<?= Security::e(Security::csrfToken()) ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Log Out</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if (!empty($_SESSION['admin_logged_in'])): ?>
<nav class="admin-nav" aria-label="Admin sections">
    <div class="container admin-nav-inner">
        <a href="<?= BASE_URL ?>/admin">Dashboard</a>
        <a href="<?= BASE_URL ?>/admin/pages">Pages</a>
        <a href="<?= BASE_URL ?>/admin/appointments">Appointments</a>
        <a href="<?= BASE_URL ?>/admin/content/features">Why Us</a>
        <a href="<?= BASE_URL ?>/admin/content/offers">Offers</a>
        <a href="<?= BASE_URL ?>/admin/content/gallery">Gallery</a>
        <a href="<?= BASE_URL ?>/admin/content/testimonials">Testimonials</a>
        <a href="<?= BASE_URL ?>/admin/content/team">Team</a>
        <a href="<?= BASE_URL ?>/admin/content/plans">Plans</a>
        <a href="<?= BASE_URL ?>/admin/content/services">Services</a>
        <a href="<?= BASE_URL ?>/admin/users">Users</a>
        <a href="<?= BASE_URL ?>/admin/password">Password</a>
    </div>
</nav>
<?php endif; ?>

<main class="admin-main">
    <div class="container">
        <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= Security::e($flash['type']) ?>">
                <?= Security::e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <?php require APP_ROOT . '/app/views/' . $view . '.php'; ?>
    </div>
</main>

<footer class="admin-footer">
    <div class="container">
        <span>&copy; <span data-year>2026</span> <?= Security::e(SITE_NAME) ?> — Admin Panel</span>
        <span>Data stored in the <code>wellness</code> MySQL database</span>
    </div>
</footer>

<!-- ============ Auto-lock screen (idle lock) ============ -->
<div class="admin-lock" id="adminLock" hidden>
    <div class="admin-lock-card">
        <span class="brand-mark" aria-hidden="true"><svg class="icon"><use href="#icon-shield"/></svg></span>
        <h2>Admin Panel Locked</h2>
        <p id="adminLockMsg">You were idle for a while. Enter your password to continue.</p>
        <form id="adminLockForm" class="admin-lock-form">
            <input type="hidden" name="csrf_token" value="<?= Security::e(Security::csrfToken()) ?>">
            <input type="password" id="adminLockPassword" name="password" placeholder="Your password" autocomplete="current-password" required>
            <button type="submit" class="btn btn-primary btn-block">Unlock <svg class="icon"><use href="#icon-check"/></svg></button>
        </form>
        <p class="admin-lock-error" id="adminLockError" hidden></p>
        <form action="<?= BASE_URL ?>/admin/logout" method="post" class="admin-lock-logout">
            <input type="hidden" name="csrf_token" value="<?= Security::e(Security::csrfToken()) ?>">
            <button type="submit" class="admin-link">Log out instead</button>
        </form>
    </div>
</div>

<script src="<?= BASE_URL ?>/public/js/main.js"></script>
</body>
</html>
