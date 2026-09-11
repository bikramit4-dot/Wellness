<?php
/**
 * Seed the wellness database from the static content files.
 *
 * Usage (from the project root):
 *   /Applications/XAMPP/xamppfiles/bin/php app/scripts/seed.php
 *
 * Creates the tables if missing, upserts therapies + posts from
 * app/data/*.php, and migrates any appointments saved in
 * storage/appointments.txt into the database.
 */

require_once __DIR__ . '/../core/Dotenv.php';
Dotenv::load(dirname(__DIR__, 2) . '/.env');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

$pdo = Database::pdo();
if ($pdo === null) {
    fwrite(STDERR, "ERROR: could not connect to MySQL.\n");
    exit(1);
}

$pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
    id          CHAR(16)     NOT NULL,
    name        VARCHAR(120) NOT NULL,
    email       VARCHAR(190) NOT NULL,
    phone       VARCHAR(40)  NOT NULL DEFAULT '',
    treatment   VARCHAR(120) NOT NULL DEFAULT '',
    date        VARCHAR(20)  NOT NULL DEFAULT '',
    time        VARCHAR(20)  NOT NULL DEFAULT '',
    message     TEXT         NULL,
    status      ENUM('new', 'confirmed', 'rejected', 'completed') NOT NULL DEFAULT 'new',
    created_at  DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_created_at (created_at),
    KEY idx_status (status)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS qr_payments (
    id             CHAR(16)     NOT NULL,
    name           VARCHAR(120) NOT NULL,
    email          VARCHAR(190) NOT NULL,
    phone          VARCHAR(40)  NOT NULL DEFAULT '',
    address        VARCHAR(300) NOT NULL DEFAULT '',
    amount         VARCHAR(40)  NOT NULL DEFAULT '',
    transaction_id VARCHAR(120) NOT NULL DEFAULT '',
    message        TEXT         NULL,
    screenshot     VARCHAR(300) NOT NULL DEFAULT '',
    status         ENUM('new', 'verified', 'rejected') NOT NULL DEFAULT 'new',
    created_at     DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_created_at (created_at),
    KEY idx_status (status)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS therapies (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    slug        VARCHAR(80)  NOT NULL,
    category    VARCHAR(40)  NOT NULL,
    title       VARCHAR(120) NOT NULL,
    icon        VARCHAR(40)  NOT NULL DEFAULT 'icon-leaf',
    image       VARCHAR(300) NOT NULL DEFAULT '',
    intro       TEXT         NULL,
    about       TEXT         NULL,
    methods     TEXT         NULL,
    benefits    TEXT         NULL,
    PRIMARY KEY (slug),
    KEY idx_category (category)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

// Older databases may predate the numeric id column used by the admin
// content manager — add it if missing (idempotent migration).
$therapiesCols = $pdo->query('SHOW COLUMNS FROM therapies')->fetchAll();
$hasTherapyId = false;
foreach ($therapiesCols as $c) {
    if (($c['Field'] ?? '') === 'id') {
        $hasTherapyId = true;
        break;
    }
}
if (!$hasTherapyId) {
    $pdo->exec('ALTER TABLE therapies ADD COLUMN id INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE FIRST');
    echo "Therapies id column added.\n";
}

$pdo->exec("CREATE TABLE IF NOT EXISTS posts (
    slug        VARCHAR(80)  NOT NULL,
    title       VARCHAR(160) NOT NULL,
    category    VARCHAR(40)  NOT NULL DEFAULT 'Wellness',
    date        VARCHAR(30)  NOT NULL DEFAULT '',
    image       VARCHAR(300) NOT NULL DEFAULT '',
    excerpt     TEXT         NULL,
    content     TEXT         NULL,
    key_points  TEXT         NULL,
    PRIMARY KEY (slug)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS gallery (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(160) NOT NULL,
    description TEXT         NULL,
    image       VARCHAR(300) NOT NULL DEFAULT '',
    alt         VARCHAR(200) NOT NULL DEFAULT '',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS testimonials (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    role        VARCHAR(120) NOT NULL DEFAULT '',
    initials    VARCHAR(8)   NOT NULL DEFAULT '',
    avatar      VARCHAR(8)   NOT NULL DEFAULT 'a1',
    rating      TINYINT      NOT NULL DEFAULT 5,
    quote       TEXT         NULL,
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS team (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    role        VARCHAR(160) NOT NULL DEFAULT '',
    image       VARCHAR(300) NOT NULL DEFAULT '',
    initials    VARCHAR(8)   NOT NULL DEFAULT '',
    avatar      VARCHAR(8)   NOT NULL DEFAULT 'a1',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

$teamCols = $pdo->query('SHOW COLUMNS FROM team')->fetchAll();
$hasTeamImage = false;
foreach ($teamCols as $column) {
    if (($column['Field'] ?? '') === 'image') {
        $hasTeamImage = true;
        break;
    }
}
if (!$hasTeamImage) {
    $pdo->exec("ALTER TABLE team ADD COLUMN image VARCHAR(300) NOT NULL DEFAULT '' AFTER role");
    echo "Team image column added.\n";
}

$pdo->exec("CREATE TABLE IF NOT EXISTS tariff_plans (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    description TEXT         NULL,
    price       VARCHAR(20)  NOT NULL DEFAULT '',
    period      VARCHAR(30)  NOT NULL DEFAULT '',
    features    TEXT         NULL,
    featured    TINYINT(1)   NOT NULL DEFAULT 0,
    badge       VARCHAR(60)  NOT NULL DEFAULT '',
    cta_label   VARCHAR(60)  NOT NULL DEFAULT 'Book a Session',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS tariff_services (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service     VARCHAR(160) NOT NULL,
    duration    VARCHAR(40)  NOT NULL DEFAULT '',
    price       VARCHAR(20)  NOT NULL DEFAULT '',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS features (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(160) NOT NULL,
    description TEXT         NULL,
    image       VARCHAR(300) NOT NULL DEFAULT '',
    icon        VARCHAR(40)  NOT NULL DEFAULT 'icon-leaf',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS offers (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(160) NOT NULL,
    description TEXT         NULL,
    image       VARCHAR(300) NOT NULL DEFAULT '',
    icon        VARCHAR(40)  NOT NULL DEFAULT 'icon-leaf',
    link        VARCHAR(200) NOT NULL DEFAULT '/treatments',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(60)  NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    display_name  VARCHAR(120) NOT NULL DEFAULT '',
    created_at    DATETIME     NOT NULL,
    updated_at    DATETIME     NOT NULL,
    UNIQUE KEY uq_admin_username (username)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

$pdo->exec("CREATE TABLE IF NOT EXISTS page_sections (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_key     VARCHAR(60)  NOT NULL,
    section_key  VARCHAR(80)  NOT NULL,
    kicker       VARCHAR(200) NOT NULL DEFAULT '',
    heading      VARCHAR(300) NOT NULL DEFAULT '',
    content      TEXT         NULL,
    sub_content  TEXT         NULL,
    image        VARCHAR(300) NOT NULL DEFAULT '',
    media        VARCHAR(300) NOT NULL DEFAULT '',
    link         VARCHAR(300) NOT NULL DEFAULT '',
    link_label   VARCHAR(160) NOT NULL DEFAULT '',
    extras       TEXT         NULL,
    UNIQUE KEY uq_page_section (page_key, section_key)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci");

echo "Tables ready.\n";

/* ---------- Therapies ---------- */
$therapies = require APP_ROOT . '/app/data/therapies.php';
$stmt = $pdo->prepare(
    'INSERT INTO therapies (slug, category, title, icon, image, intro, about, methods, benefits)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        category = VALUES(category), title = VALUES(title), icon = VALUES(icon),
        image = VALUES(image), intro = VALUES(intro), about = VALUES(about),
        methods = VALUES(methods), benefits = VALUES(benefits)'
);
$count = 0;
foreach ($therapies as $slug => $t) {
    $stmt->execute([
        $slug,
        $t['category'] ?? '',
        $t['title'] ?? '',
        $t['icon'] ?? 'icon-leaf',
        $t['image'] ?? '',
        $t['intro'] ?? '',
        json_encode($t['about'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        json_encode($t['methods'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        json_encode($t['benefits'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);
    $count++;
}
echo "Therapies seeded: $count\n";

/* ---------- Posts ---------- */
$posts = require APP_ROOT . '/app/data/posts.php';
$stmt = $pdo->prepare(
    'INSERT INTO posts (slug, title, category, date, image, excerpt, content, key_points)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        title = VALUES(title), category = VALUES(category), date = VALUES(date),
        image = VALUES(image), excerpt = VALUES(excerpt), content = VALUES(content),
        key_points = VALUES(key_points)'
);
$count = 0;
foreach ($posts as $slug => $p) {
    $stmt->execute([
        $slug,
        $p['title'] ?? '',
        $p['category'] ?? 'Wellness',
        $p['date'] ?? '',
        $p['image'] ?? '',
        $p['excerpt'] ?? '',
        json_encode($p['content'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        json_encode($p['keyPoints'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);
    $count++;
}
echo "Posts seeded: $count\n";

/* ---------- Gallery ---------- */
$gallery = require APP_ROOT . '/app/data/gallery.php';
$pdo->exec('DELETE FROM gallery');
$stmt = $pdo->prepare('INSERT INTO gallery (title, description, image, alt, sort_order) VALUES (?, ?, ?, ?, ?)');
$i = 0;
foreach ($gallery as $item) {
    $stmt->execute([
        $item['title'] ?? '',
        $item['description'] ?? '',
        $item['image'] ?? '',
        $item['alt'] ?? '',
        ++$i,
    ]);
}
echo "Gallery items seeded: " . count($gallery) . "\n";

/* ---------- Testimonials ---------- */
$testimonials = require APP_ROOT . '/app/data/testimonials.php';
$pdo->exec('DELETE FROM testimonials');
$stmt = $pdo->prepare('INSERT INTO testimonials (name, role, initials, avatar, rating, quote, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)');
$i = 0;
foreach ($testimonials as $t) {
    $stmt->execute([
        $t['name'] ?? '',
        $t['role'] ?? '',
        $t['initials'] ?? '',
        $t['avatar'] ?? 'a1',
        $t['rating'] ?? 5,
        $t['quote'] ?? '',
        ++$i,
    ]);
}
echo "Testimonials seeded: " . count($testimonials) . "\n";

/* ---------- Offers (What We Offer) ---------- */
$offers = require APP_ROOT . '/app/data/offers.php';
$pdo->exec('DELETE FROM offers');
$stmt = $pdo->prepare('INSERT INTO offers (title, description, image, icon, link, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
$i = 0;
foreach ($offers as $offer) {
    $stmt->execute([
        $offer['title'] ?? '',
        $offer['description'] ?? '',
        $offer['image'] ?? '',
        $offer['icon'] ?? 'icon-leaf',
        $offer['link'] ?? '/treatments',
        ++$i,
    ]);
}
echo "Offers seeded: " . count($offers) . "\n";

/* ---------- Features (Why Choose Us) ---------- */
$features = require APP_ROOT . '/app/data/features.php';
$pdo->exec('DELETE FROM features');
$stmt = $pdo->prepare('INSERT INTO features (title, description, image, icon, sort_order) VALUES (?, ?, ?, ?, ?)');
$i = 0;
foreach ($features as $feature) {
    $stmt->execute([
        $feature['title'] ?? '',
        $feature['description'] ?? '',
        $feature['image'] ?? '',
        $feature['icon'] ?? 'icon-leaf',
        ++$i,
    ]);
}
echo "Features seeded: " . count($features) . "\n";

/* ---------- Admin users ---------- */
// Seed the initial admin user (matching app/config/config.php) only when the
// table is empty, so the panel always has at least one usable login.
$hasAdmin = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
if ($hasAdmin === 0) {
    $stmt = $pdo->prepare(
        'INSERT INTO admin_users (username, password_hash, display_name, created_at, updated_at)
         VALUES (?, ?, ?, NOW(), NOW())'
    );
    $stmt->execute([
        defined('ADMIN_USERNAME') ? ADMIN_USERNAME : 'admin',
        defined('ADMIN_PASSWORD_HASH') ? ADMIN_PASSWORD_HASH : '',
        'Administrator',
    ]);
    echo "Admin user seeded.\n";
} else {
    echo "Admin users already present (" . $hasAdmin . "). Skipping.\n";
}

/* ---------- Page sections (per-page editable content) ---------- */
$pageSections = require APP_ROOT . '/app/data/page_sections.php';
$pdo->exec('DELETE FROM page_sections');
$stmt = $pdo->prepare(
    'INSERT INTO page_sections (page_key, section_key, kicker, heading, content, sub_content, image, media, link, link_label, extras)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$count = 0;
foreach ($pageSections as $pageKey => $sections) {
    foreach ($sections as $sectionKey => $fields) {
        $stmt->execute([
            $pageKey,
            $sectionKey,
            (string) ($fields['kicker'] ?? ''),
            (string) ($fields['heading'] ?? ''),
            (string) ($fields['content'] ?? ''),
            (string) ($fields['sub_content'] ?? ''),
            (string) ($fields['image'] ?? ''),
            (string) ($fields['media'] ?? ''),
            (string) ($fields['link'] ?? ''),
            (string) ($fields['link_label'] ?? ''),
            json_encode($fields['extras'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
        $count++;
    }
}
echo "Page sections seeded: $count\n";

/* ---------- Team ---------- */
$team = require APP_ROOT . '/app/data/team.php';
$pdo->exec('DELETE FROM team');
$stmt = $pdo->prepare('INSERT INTO team (name, role, image, initials, avatar, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
$i = 0;
foreach ($team as $member) {
    $stmt->execute([
        $member['name'] ?? '',
        $member['role'] ?? '',
        $member['image'] ?? '',
        $member['initials'] ?? '',
        $member['avatar'] ?? 'a1',
        ++$i,
    ]);
}
echo "Team members seeded: " . count($team) . "\n";

/* ---------- Tariff (plans + services) ---------- */
$tariff = require APP_ROOT . '/app/data/tariff.php';
$pdo->exec('DELETE FROM tariff_plans');
$stmt = $pdo->prepare(
    'INSERT INTO tariff_plans (name, description, price, period, features, featured, badge, cta_label, sort_order)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$i = 0;
foreach (($tariff['plans'] ?? []) as $plan) {
    $stmt->execute([
        $plan['name'] ?? '',
        $plan['description'] ?? '',
        $plan['price'] ?? '',
        $plan['period'] ?? '',
        json_encode($plan['features'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        !empty($plan['featured']) ? 1 : 0,
        $plan['badge'] ?? '',
        $plan['ctaLabel'] ?? 'Book a Session',
        ++$i,
    ]);
}
echo "Tariff plans seeded: " . count($tariff['plans'] ?? []) . "\n";

$pdo->exec('DELETE FROM tariff_services');
$stmt = $pdo->prepare('INSERT INTO tariff_services (service, duration, price, sort_order) VALUES (?, ?, ?, ?)');
$i = 0;
foreach (($tariff['services'] ?? []) as $svc) {
    $stmt->execute([
        $svc['service'] ?? '',
        $svc['duration'] ?? '',
        $svc['price'] ?? '',
        ++$i,
    ]);
}
echo "Tariff services seeded: " . count($tariff['services'] ?? []) . "\n";

/* ---------- Appointments (migrate from text file) ---------- */
$file = APP_ROOT . '/storage/appointments.txt';
$migrated = 0;
if (is_file($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $insert = $pdo->prepare(
        'INSERT IGNORE INTO appointments (id, name, email, phone, treatment, date, time, message, status, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    foreach ($lines as $line) {
        $row = json_decode($line, true);
        if (!is_array($row) || empty($row['id'])) {
            continue;
        }
        $insert->execute([
            $row['id'],
            $row['name'] ?? '',
            $row['email'] ?? '',
            $row['phone'] ?? '',
            $row['treatment'] ?? '',
            $row['date'] ?? '',
            $row['time'] ?? '',
            $row['message'] ?? null,
            $row['status'] ?? 'new',
            $row['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
        $migrated++;
    }
}
echo "Appointments migrated from file: $migrated\n";

echo "Done. Database '" . DB_NAME . "' is ready.\n";
