#!/usr/bin/env php
<?php
/**
 * Quick database diagnostic — run from the project root:
 *   php app/scripts/check-db.php
 */

require_once __DIR__ . '/../core/Dotenv.php';
Dotenv::load(dirname(__DIR__, 2) . '/.env');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

echo "=== Database Diagnostic ===" . PHP_EOL . PHP_EOL;

echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : '(not defined)') . PHP_EOL;
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : '(not defined)') . PHP_EOL;
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : '(not defined)') . PHP_EOL;
echo "DB_PASS: " . (defined('DB_PASS') ? (DB_PASS !== '' ? '(set)' : '(empty)') : '(not defined)') . PHP_EOL;
echo PHP_EOL;

$pdo = Database::pdo();
if ($pdo === null) {
    echo "❌ Database connection FAILED" . PHP_EOL;
    echo "   Check DB_HOST, DB_NAME, DB_USER, DB_PASS in your .env file." . PHP_EOL;
    exit(1);
}

echo "✅ Database connection OK" . PHP_EOL . PHP_EOL;

// Check tables
$tables = ['page_sections', 'therapies', 'posts', 'gallery', 'testimonials', 'team', 'tariff_plans', 'tariff_services', 'features', 'offers', 'admin_users', 'appointments', 'qr_payments'];

foreach ($tables as $table) {
    $exists = $pdo->query("SHOW TABLES LIKE '$table'")->fetchColumn();
    if ($exists) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "✅ $table exists ($count rows)" . PHP_EOL;
    } else {
        echo "❌ $table MISSING" . PHP_EOL;
    }
}

echo PHP_EOL;

// Check page_sections specifically
echo "=== page_sections table check ===" . PHP_EOL;
$check = $pdo->query("DESCRIBE page_sections")->fetchAll(PDO::FETCH_COLUMN);
if ($check) {
    echo "Columns: " . implode(', ', $check) . PHP_EOL;

    $brandRow = $pdo->query("SELECT * FROM page_sections WHERE page_key = 'site' AND section_key = 'brand'")->fetch();
    if ($brandRow) {
        echo "✅ Brand section exists (id=" . $brandRow['id'] . ")" . PHP_EOL;
    } else {
        echo "⚠️  Brand section does NOT exist yet (will be created on first save)" . PHP_EOL;
    }
} else {
    echo "❌ Cannot describe page_sections table" . PHP_EOL;
}

echo PHP_EOL . "Done." . PHP_EOL;
