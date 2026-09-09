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
    <link rel="stylesheet" href="<?= asset_url('/css/style.css') ?>">
</head>
<body class="admin-body<?= !empty($_SESSION['admin_logged_in']) ? ' admin-authed' : '' ?>" data-base="<?= Security::e(BASE_URL) ?>" data-idle-lock="<?= Security::e((string) (defined('ADMIN_IDLE_LOCK_SECONDS') ? ADMIN_IDLE_LOCK_SECONDS : 20)) ?>" data-lock-entry="<?= Security::e((defined('ADMIN_LOCK_ON_ENTRY') && ADMIN_LOCK_ON_ENTRY) ? '1' : '0') ?>" data-csrf="<?= Security::e(Security::csrfToken()) ?>" data-notif-ts="<?= time() ?>">

<!-- ============ SVG Icon Sprite ============ -->
<?php require APP_ROOT . '/app/views/partials/icons.php'; ?>

<header class="admin-topbar">
    <div class="container admin-topbar-inner">
        <a class="brand" href="<?= BASE_URL ?>/admin">
            <span class="brand-mark" aria-hidden="true"><svg class="icon"><use href="#icon-shield"/></svg></span>
            <span>Chitrawan <em>Admin</em></span>
        </a>
        <div class="admin-topbar-actions">
            <a class="admin-link" href="<?= BASE_URL ?>/">View Site</a>
            <?php if (!empty($_SESSION['admin_logged_in'])): ?>
                <?php $__notifCount = NotificationModel::unreadCount(); ?>
                <div class="notif-bell" id="notifBell">
                    <button class="notif-bell-btn" id="notifBellBtn" title="Notifications">
                        <svg class="icon"><use href="#icon-zap"/></svg>
                        <?php if ($__notifCount > 0): ?>
                            <span class="notif-badge"><?= (int) $__notifCount ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="notif-dropdown" id="notifDropdown" hidden>
                        <div class="notif-dropdown-head">
                            <h3>Notifications</h3>
                            <?php if ($__notifCount > 0): ?>
                                <form action="<?= BASE_URL ?>/admin/notifications/read-all" method="post" class="notif-mark-all">
                                    <input type="hidden" name="csrf_token" value="<?= Security::e(Security::csrfToken()) ?>">
                                    <button type="submit">Mark all read</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <ul class="notif-list" id="notifList">
                            <?php $__notifs = NotificationModel::all(); ?>
                            <?php if (empty($__notifs)): ?>
                                <li class="notif-empty">No notifications yet</li>
                            <?php else: ?>
                                <?php foreach (array_slice($__notifs, 0, 15) as $__n): ?>
                                    <li class="notif-item <?= empty($__n['read']) ? 'unread' : '' ?>" data-id="<?= Security::e($__n['id'] ?? '') ?>">
                                        <a href="<?= Security::e(BASE_URL . ($__n['link'] ?? '/admin')) ?>" class="notif-item-link">
                                            <span class="notif-icon notif-icon-<?= Security::e($__n['type'] ?? 'system') ?>">
                                                <?php if (($__n['type'] ?? '') === 'appointment'): ?>
                                                    <svg class="icon"><use href="#icon-calendar"/></svg>
                                                <?php elseif (($__n['type'] ?? '') === 'qr_payment'): ?>
                                                    <svg class="icon"><use href="#icon-zap"/></svg>
                                                <?php else: ?>
                                                    <svg class="icon"><use href="#icon-shield"/></svg>
                                                <?php endif; ?>
                                            </span>
                                            <div class="notif-item-body">
                                                <strong><?= Security::e($__n['title'] ?? '') ?></strong>
                                                <span><?= Security::e($__n['message'] ?? '') ?></span>
                                                <time><?= Security::e($__n['created_at'] ?? '') ?></time>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <span class="admin-user">
                    <?php if (!empty($_SESSION['admin_avatar'])): ?>
                        <img src="<?= Security::e($_SESSION['admin_avatar']) ?>" alt="" class="admin-avatar">
                    <?php endif; ?>
                    Signed in as <strong><?= Security::e($_SESSION['admin_display_name'] ?? ($_SESSION['admin_username'] ?? 'Admin')) ?></strong>
                    <?php if (($_SESSION['admin_role'] ?? 'admin') === 'staff'): ?>
                        <span class="admin-auth-badge" style="background:#1e40af;">Staff</span>
                    <?php elseif (($_SESSION['admin_auth_method'] ?? '') === 'google'): ?>
                        <span class="admin-auth-badge">Google</span>
                    <?php endif; ?>
                </span>
                <form action="<?= BASE_URL ?>/admin/logout" method="post">
                    <input type="hidden" name="csrf_token" value="<?= Security::e(Security::csrfToken()) ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Log Out</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if (!empty($_SESSION['admin_logged_in'])): ?>
<?php $__currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH); $__currentPath = preg_replace('#^' . preg_quote(BASE_URL, '#') . '#', '', $__currentPath); ?>
<?php $__isAdmin = (($_SESSION['admin_role'] ?? 'admin') === 'admin'); ?>
<nav class="admin-sidebar" aria-label="Admin sections">
    <!-- Bookings Section (visible to ALL users) -->
    <div class="sidebar-section">
        <span class="sidebar-section-title">Bookings</span>
        <a href="<?= BASE_URL ?>/admin" class="sidebar-link <?= $__currentPath === '/admin' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-calendar"/></svg> Dashboard
        </a>
        <a href="<?= BASE_URL ?>/admin/appointments" class="sidebar-link <?= $__currentPath === '/admin/appointments' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-zap"/></svg> Appointments
            <?php $__apptNew = (new AppointmentModel())->countByStatus('new'); if ($__apptNew > 0): ?> <span class="sidebar-badge"><?= (int) $__apptNew ?></span><?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>/admin/qr-payments" class="sidebar-link <?= $__currentPath === '/admin/qr-payments' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-zap"/></svg> QR Payments
            <?php $__qrNew = AdminController::qrNewCount(); if ($__qrNew > 0): ?> <span class="sidebar-badge"><?= (int) $__qrNew ?></span><?php endif; ?>
        </a>
    </div>

    <?php if ($__isAdmin): ?>
    <!-- Content Section (admin only) -->
    <div class="sidebar-section">
        <span class="sidebar-section-title">Content</span>
        <a href="<?= BASE_URL ?>/admin/content/gallery" class="sidebar-link <?= $__currentPath === '/admin/content/gallery' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-sun"/></svg> Gallery
        </a>
        <a href="<?= BASE_URL ?>/admin/content/testimonials" class="sidebar-link <?= $__currentPath === '/admin/content/testimonials' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-star"/></svg> Testimonials
        </a>
        <a href="<?= BASE_URL ?>/admin/reviews" class="sidebar-link <?= $__currentPath === '/admin/reviews' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-star"/></svg> Customer Reviews
        </a>
        <a href="<?= BASE_URL ?>/admin/content/team" class="sidebar-link <?= $__currentPath === '/admin/content/team' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-users"/></svg> Team
        </a>
        <a href="<?= BASE_URL ?>/admin/content/features" class="sidebar-link <?= $__currentPath === '/admin/content/features' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-heart"/></svg> Why Us
        </a>
        <a href="<?= BASE_URL ?>/admin/content/offers" class="sidebar-link <?= $__currentPath === '/admin/content/offers' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-zap"/></svg> Offers
        </a>
        <a href="<?= BASE_URL ?>/admin/content/therapies" class="sidebar-link <?= $__currentPath === '/admin/content/therapies' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-activity"/></svg> Therapies
        </a>
        <a href="<?= BASE_URL ?>/admin/content/plans" class="sidebar-link <?= $__currentPath === '/admin/content/plans' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-heart"/></svg> Plans
        </a>
        <a href="<?= BASE_URL ?>/admin/content/services" class="sidebar-link <?= $__currentPath === '/admin/content/services' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-clock"/></svg> Services
        </a>
    </div>

    <!-- Pages Section (admin only) -->
    <div class="sidebar-section">
        <span class="sidebar-section-title">Pages</span>
        <a href="<?= BASE_URL ?>/admin/pages" class="sidebar-link <?= str_starts_with($__currentPath, '/admin/pages') ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-map-pin"/></svg> Page Editor
        </a>
    </div>

    <!-- Settings Section (admin only) -->
    <div class="sidebar-section">
        <span class="sidebar-section-title">Settings</span>
        <a href="<?= BASE_URL ?>/admin/users" class="sidebar-link <?= str_starts_with($__currentPath, '/admin/users') ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-users"/></svg> Users
        </a>
        <a href="<?= BASE_URL ?>/admin/password" class="sidebar-link <?= $__currentPath === '/admin/password' ? 'active' : '' ?>">
            <svg class="icon"><use href="#icon-shield"/></svg> Password
        </a>
        <form action="<?= BASE_URL ?>/admin/clear-cache" method="post" class="sidebar-link sidebar-link-form">
            <input type="hidden" name="csrf_token" value="<?= Security::e(Security::csrfToken()) ?>">
            <button type="submit" title="Clear PHP opcache & file cache">
                <svg class="icon"><use href="#icon-zap"/></svg> Clear Cache
            </button>
        </form>
    </div>
    <?php endif; ?>
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

<script src="<?= asset_url('/js/main.js') ?>"></script>
</body>
</html>
