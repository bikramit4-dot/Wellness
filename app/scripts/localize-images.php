<?php
/**
 * One-off migration: localize every images.unsplash.com photo.
 *
 * Rewrites references in:
 *   - app/data/*.php        → BASE_URL . '/public/uploads/photos/photo-<id>.jpg'
 *   - app/views/pages/*.php → <?= BASE_URL ?>/public/uploads/photos/photo-<id>.jpg
 *   - public/css/style.css  → ../uploads/photos/photo-<id>.jpg (relative URL)
 *   - database rows         → BASE_URL . '/public/uploads/photos/photo-<id>.jpg'
 *
 * Run with XAMPP's PHP so the DB connection uses the right socket:
 *   /Applications/XAMPP/xamppfiles/bin/php app/scripts/localize-images.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

// Matches any images.unsplash.com URL, capturing the photo id.
$pattern = '#https://images\.unsplash\.com/photo-([0-9a-zA-Z-]+)(?:\?[^"\']*)?#';

/* ---------- 1) Data files ---------- */
foreach (['therapies', 'posts', 'gallery', 'features'] as $name) {
    $path = APP_ROOT . '/app/data/' . $name . '.php';
    if (!is_file($path)) {
        continue;
    }
    $content = (string) file_get_contents($path);
    $new = preg_replace($pattern, "BASE_URL . '/public/uploads/photos/photo-\\1.jpg'", $content);
    if ($new !== $content) {
        file_put_contents($path, $new);
        echo "$name.php updated\n";
    }
}

/* ---------- 2) Page templates ---------- */
foreach (['about', 'home'] as $name) {
    $path = APP_ROOT . '/app/views/pages/' . $name . '.php';
    if (!is_file($path)) {
        continue;
    }
    $content = (string) file_get_contents($path);
    $new = preg_replace($pattern, '<?= BASE_URL ?>/public/uploads/photos/photo-\\1.jpg', $content);
    if ($new !== $content) {
        file_put_contents($path, $new);
        echo "views/pages/$name.php updated\n";
    }
}

/* ---------- 3) CSS (relative to public/css/) ---------- */
$css = APP_ROOT . '/public/css/style.css';
$content = (string) file_get_contents($css);
$new = preg_replace($pattern, '../uploads/photos/photo-\\1.jpg', $content);
if ($new !== $content) {
    file_put_contents($css, $new);
    echo "style.css updated\n";
}

/* ---------- 4) Database rows ---------- */
$pdo = Database::pdo();
if ($pdo === null) {
    fwrite(STDERR, "ERROR: could not connect to MySQL.\n");
    exit(1);
}

$toLocal = static function (string $url): ?string {
    if (preg_match('#https://images\.unsplash\.com/photo-([0-9a-zA-Z-]+)#', $url, $m)) {
        return BASE_URL . '/public/uploads/photos/photo-' . $m[1] . '.jpg';
    }

    return null;
};

$tables = [
    'gallery' => 'id',
    'features' => 'id',
    'therapies' => 'slug',
    'posts' => 'slug',
];

foreach ($tables as $table => $pk) {
    $rows = $pdo->query("SELECT `$pk` AS pk, image FROM `$table` WHERE image <> ''")->fetchAll();
    $stmt = $pdo->prepare("UPDATE `$table` SET image = ? WHERE `$pk` = ?");
    $n = 0;
    foreach ($rows as $r) {
        $local = $toLocal((string) $r['image']);
        if ($local !== null && $local !== $r['image']) {
            $stmt->execute([$local, $r['pk']]);
            $n++;
        }
    }
    echo "$table: $n rows updated\n";
}

echo "Migration complete.\n";
