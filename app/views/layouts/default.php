<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= Security::e($title ?? SITE_NAME) ?> | <?= Security::e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset_url('/css/style.css') ?>">
</head>
<body data-base="<?= Security::e(BASE_URL) ?>">

<?php
// View helpers (sec(), sec_url(), sec_social_url(), …) — loaded before the
// header partials render, since the nav uses sec_social_url(). require_once:
// page views include this too.
require_once APP_ROOT . '/app/views/partials/section_helpers.php';
?>
<!-- ============ SVG Icon Sprite ============ -->
<?php require APP_ROOT . '/app/views/partials/icons.php'; ?>

<?php
// Load site-wide sections for the top bar (shared with brand.php)
if (!isset($GLOBALS['__siteSectionsLoaded'])) {
    $GLOBALS['__siteSectionsLoaded'] = true;
    $GLOBALS['__siteSections'] = PageSectionModel::forPage('site');
}
$_topbar = $GLOBALS['__siteSections']['topbar'] ?? [];
$_topbarPhone = trim((string) ($_topbar['link'] ?? '+977 56-535213'));
$_topbarEmail = trim((string) ($_topbar['sub_content'] ?? 'nchchitwan@gmail.com'));
$_topbarHours = trim((string) ($_topbar['kicker'] ?? 'Sun – Fri: 8:00 AM – 7:00 PM'));
?>
<!-- ============ Utility Top Bar ============ -->
<div class="topbar">
    <div class="container">
        <div class="topbar-info">
            <a href="tel:<?= Security::e(preg_replace('/[^+\d]/', '', $_topbarPhone)) ?>"><svg class="icon icon-sm"><use href="#icon-phone"/></svg> <?= Security::e($_topbarPhone) ?></a>
            <a href="mailto:<?= Security::e($_topbarEmail) ?>"><svg class="icon icon-sm"><use href="#icon-mail"/></svg> <?= Security::e($_topbarEmail) ?></a>
        </div>
        <div class="topbar-hours"><svg class="icon icon-sm"><use href="#icon-clock"/></svg> <?= Security::e($_topbarHours) ?></div>
    </div>
</div>

<!-- ============ Header / Navigation ============ -->
<header class="site-header" id="siteHeader">
    <div class="container nav-wrap">
        <?php require APP_ROOT . '/app/views/partials/brand.php'; ?>
        <?php
// ---- Navigation menu -------------------------------------------------
// Fully admin-manageable (Admin → Pages → Site Settings → Navigation Menu):
//   • Custom links ("Label | URL" per line) replace the standard menu.
//   • With no custom links, the standard menu is used and the four therapy
//     dropdowns are AUTO-GENERATED from the therapies table (Admin →
//     Content → Therapies), so adding/renaming a therapy updates the menu.
$_navCustom = (array) ($GLOBALS['__siteSections']['nav']['extras'] ?? []);
$_navCustom = array_values(array_filter(array_map('trim', $_navCustom), static fn ($l) => $l !== ''));

$_navGroups = [
    ['/treatments', 'Treatments'],
    ['/physiotherapy', 'Physiotherapy'],
    ['/diet-therapy', 'Diet Therapy'],
    ['/special-therapies', 'Special Therapies'],
];
if ($_navCustom === []) {
    $_navTherapies = TherapyModel::all();
    $_navByCategory = [];
    foreach ($_navTherapies as $slug => $t) {
        $_navByCategory[$t['category']][] = ['slug' => $slug, 'title' => (string) $t['title']];
    }
    foreach ($_navByCategory as $cat => $items) {
        usort($items, static fn (array $a, array $b): int => strcasecmp($a['title'], $b['title']));
        $_navByCategory[$cat] = $items;
    }
}
?>
<nav class="main-nav" id="mainNav" aria-label="Main navigation">
    <?php if ($_navCustom !== []): ?>
        <?php foreach ($_navCustom as $_navLine): ?>
            <?php
            $_bits = array_map('trim', explode('|', $_navLine));
            $_label = $_bits[0];
            $_href = sec_social_url($_bits[1] ?? '#');
            ?>
            <a class="nav-link" href="<?= Security::e($_href) ?>"><?= Security::e($_label) ?></a>
        <?php endforeach; ?>
    <?php else: ?>
        <a class="nav-link" href="<?= BASE_URL ?>/">Home</a>
        <div class="dropdown">
            <a class="nav-link dropdown-toggle" href="<?= BASE_URL ?>/about" aria-haspopup="true" aria-expanded="false">About <svg class="icon icon-sm"><use href="#icon-chevron"/></svg></a>
            <div class="dropdown-menu">
                <a href="<?= BASE_URL ?>/about">About Us</a>
                <a href="<?= BASE_URL ?>/about#mission">Our Mission</a>
                <a href="<?= BASE_URL ?>/about#vision">Our Vision</a>
                <a href="<?= BASE_URL ?>/about#approach">Our Core Values</a>
                <a href="<?= BASE_URL ?>/about#founder">Our Founders</a>
                <a href="<?= BASE_URL ?>/about#doctors">Our Doctors</a>
                <a href="<?= BASE_URL ?>/about#group">Our Team</a>
            </div>
        </div>
        <?php foreach ($_navGroups as [$_navPath, $_navLabel]): ?>
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="<?= BASE_URL . $_navPath ?>" aria-haspopup="true" aria-expanded="false"><?= Security::e($_navLabel) ?> <svg class="icon icon-sm"><use href="#icon-chevron"/></svg></a>
                <div class="dropdown-menu">
                    <a href="<?= BASE_URL . $_navPath ?>">All <?= Security::e($_navLabel) ?></a>
                    <?php foreach ($_navByCategory[$_navPath] ?? [] as $_navItem): ?>
                        <a href="<?= BASE_URL . $_navPath . '/' . Security::e($_navItem['slug']) ?>"><?= Security::e($_navItem['title']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <a class="nav-link" href="<?= BASE_URL ?>/tariff">Tariff</a>
        <a class="nav-link" href="<?= BASE_URL ?>/gallery">Gallery</a>
        <a class="nav-link" href="<?= BASE_URL ?>/blog">Blog</a>
        <a class="nav-link" href="<?= BASE_URL ?>/contact">Contact</a>
    <?php endif; ?>
    <a class="btn btn-primary nav-cta" href="<?= BASE_URL ?>/contact">Book Now</a>
</nav>
        <button class="nav-toggle" id="navToggle" aria-label="Open menu" aria-expanded="false">
            <svg class="icon"><use href="#icon-menu"/></svg>
        </button>
    </div>
</header>
<div class="nav-backdrop" id="navBackdrop"></div>

<!-- ============ Automatic Page Hero (inner pages) ============ -->
<?php if (($title ?? '') !== 'Home' && (($hidePageHero ?? false) !== true)): ?>
<section class="page-hero">
    <div class="page-hero-pattern" aria-hidden="true"></div>
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>/">Home</a>
            <?php if (!empty($breadcrumbParents)): ?>
                <?php foreach ($breadcrumbParents as $crumb): ?>
                    <span>/</span>
                    <a href="<?= BASE_URL ?><?= Security::e($crumb['url'] ?? '') ?>"><?= Security::e($crumb['label'] ?? '') ?></a>
                <?php endforeach; ?>
            <?php endif; ?>
            <span>/</span>
            <span><?= Security::e($title ?? '') ?></span>
        </nav>
        <h1><?= Security::e($title ?? '') ?></h1>
        <?php if (!empty($pageIntro)): ?>
            <p class="page-hero-intro"><?= Security::e($pageIntro) ?></p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<main>
    <?php if (!empty($flash)): ?>
        <div class="container">
            <div class="alert alert-<?= Security::e($flash['type']) ?>">
                <?php if ($flash['type'] === 'success'): ?><svg class="icon"><use href="#icon-check"/></svg><?php endif; ?>
                <?= Security::e($flash['message']) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php require APP_ROOT . '/app/views/' . $view . '.php'; ?>
</main>

<!-- ============ Footer ============ -->
<?php
// The footer uses section helpers (sec_social_url, …). Some page views don't
// include section_helpers.php themselves — the require_once at the top of
// this layout already covers every case (safe even when a view loaded it).

// Editable footer information (Admin → Pages → Site Settings → Footer
// Information). Maps to the shared section columns: content=tagline,
// heading=address, sub_content=email, link=phone, kicker=hours,
// link_label=copyright tagline, extras=social links.
if (!isset($GLOBALS['__siteSectionsLoaded'])) {
    $GLOBALS['__siteSectionsLoaded'] = true;
    $GLOBALS['__siteSections'] = PageSectionModel::forPage('site');
}
$footerInfo = $GLOBALS['__siteSections']['footer'] ?? [];
$fiTagline = trim((string) ($footerInfo['content'] ?? ''));
$fiAddress = trim((string) ($footerInfo['heading'] ?? ''));
$fiEmail   = trim((string) ($footerInfo['sub_content'] ?? ''));
$fiPhone   = trim((string) ($footerInfo['link'] ?? ''));
$fiHours   = trim((string) ($footerInfo['kicker'] ?? ''));
$fiCredit  = trim((string) ($footerInfo['link_label'] ?? ''));
$fiSocials = (array) ($footerInfo['extras'] ?? []);
$fiTelHref = preg_replace('/[^+\d]/', '', $fiPhone);
$fiHoursHtml = implode('<br>', array_map(
    static fn (string $l): string => Security::e(trim($l)),
    preg_split('/\r\n|\r|\n/', $fiHours, -1, PREG_SPLIT_NO_EMPTY) ?: ['']
));
?>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <?php $brandClass = 'footer-brand-name'; require APP_ROOT . '/app/views/partials/brand.php'; unset($brandClass); ?>
                <?php if ($fiTagline !== ''): ?><p><?= Security::e($fiTagline) ?></p><?php endif; ?>
                <?php if ($fiSocials !== []): ?>
                <div class="social-row">
                    <?php foreach ($fiSocials as $line): ?>
                        <?php
                        $fiLine = trim((string) $line);
                        if ($fiLine === '') { continue; }
                        if (str_contains($fiLine, '|')) {
                            [$fiLabel, $fiRawUrl] = array_pad(array_map('trim', explode('|', $fiLine, 2)), 2, '');
                        } else {
                            // No "Label | URL" split — treat the whole line as the URL.
                            $fiLabel = '';
                            $fiRawUrl = $fiLine;
                        }
                        $fiUrl = sec_social_url($fiRawUrl);
                        if ($fiLabel === '') {
                            // Derive a friendly label from the link's domain.
                            $fiHost = strtolower((string) (parse_url($fiUrl, PHP_URL_HOST) ?: ''));
                            $fiLabel = match (true) {
                                str_contains($fiHost, 'facebook') => 'Facebook',
                                str_contains($fiHost, 'instagram') => 'Instagram',
                                str_contains($fiHost, 'tiktok') => 'TikTok',
                                str_contains($fiHost, 'twitter') || in_array($fiHost, ['x.com', 'www.x.com'], true) => 'X (Twitter)',
                                str_contains($fiHost, 'youtube') || str_contains($fiHost, 'youtu.be') => 'YouTube',
                                $fiHost !== '' => ucfirst(preg_replace('/^www\./', '', $fiHost)),
                                default => 'Link',
                            };
                        }
                        // Icon: match against label AND url (label may be custom).
                        $fiLower = strtolower($fiLabel . ' ' . $fiUrl);
                        $fiIcon = 'icon-link';
                        if (str_contains($fiLower, 'face')) { $fiIcon = 'icon-facebook'; }
                        elseif (str_contains($fiLower, 'insta')) { $fiIcon = 'icon-instagram'; }
                        elseif (str_contains($fiLower, 'tiktok') || str_contains($fiLower, 'tik tok')) { $fiIcon = 'icon-tiktok'; }
                        elseif (str_contains($fiLower, 'twitter') || preg_match('~//(www\.)?x\.com~i', $fiUrl)) { $fiIcon = 'icon-twitter'; }
                        ?>
                        <a href="<?= Security::e($fiUrl) ?>" aria-label="<?= Security::e($fiLabel) ?>" target="_blank" rel="noopener"><svg class="icon"><use href="#<?= Security::e($fiIcon) ?>"/></svg></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="<?= BASE_URL ?>/">Home</a></li>
                    <li><a href="<?= BASE_URL ?>/about">About Us</a></li>
                    <li><a href="<?= BASE_URL ?>/tariff">Tariff</a></li>
                    <li><a href="<?= BASE_URL ?>/gallery">Gallery</a></li>
                    <li><a href="<?= BASE_URL ?>/blog">Blog</a></li>
                    <li><a href="<?= BASE_URL ?>/contact">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h4>Our Services</h4>
                <ul>
                    <li><a href="<?= BASE_URL ?>/treatments">Naturopathy</a></li>
                    <li><a href="<?= BASE_URL ?>/treatments">Yoga &amp; Meditation</a></li>
                    <li><a href="<?= BASE_URL ?>/physiotherapy">Physiotherapy</a></li>
                    <li><a href="<?= BASE_URL ?>/diet-therapy">Diet Therapy</a></li>
                    <li><a href="<?= BASE_URL ?>/special-therapies">Special Therapies</a></li>
                </ul>
            </div>
            <div>
                <h4>Get in Touch</h4>
                <ul class="footer-contact">
                    <?php if ($fiAddress !== ''): ?><li><svg class="icon icon-sm"><use href="#icon-map-pin"/></svg> <?= Security::e($fiAddress) ?></li><?php endif; ?>
                    <?php if ($fiPhone !== ''): ?><li><a href="tel:<?= Security::e($fiTelHref) ?>"><svg class="icon icon-sm"><use href="#icon-phone"/></svg> <?= Security::e($fiPhone) ?></a></li><?php endif; ?>
                    <?php if ($fiEmail !== ''): ?><li><a href="mailto:<?= Security::e($fiEmail) ?>"><svg class="icon icon-sm"><use href="#icon-mail"/></svg> <?= Security::e($fiEmail) ?></a></li><?php endif; ?>
                    <?php if ($fiHoursHtml !== ''): ?><li><svg class="icon icon-sm"><use href="#icon-clock"/></svg> <?= $fiHoursHtml /* pre-escaped: each line escaped + literal <br> */ ?></li><?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container copyright">
            <span>© <span data-year>2026</span> <?= Security::e(SITE_NAME) ?>. All Rights Reserved.</span>
            <?php if ($fiCredit !== ''): ?><span><?= Security::e($fiCredit) ?></span><?php endif; ?>
        </div>
    </div>
</footer>

<script src="<?= asset_url('/js/main.js') ?>"></script>
</body>
</html>
