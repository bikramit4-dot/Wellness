<?php
/**
 * Fix therapy IDs in the database.
 * 
 * This script ensures all therapies have proper numeric IDs so the admin
 * panel can edit and delete them.
 * 
 * Usage: php app/scripts/fix-therapy-ids.php
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

echo "Fixing therapy IDs...\n\n";

// Check if id column exists
$therapiesCols = $pdo->query('SHOW COLUMNS FROM therapies')->fetchAll();
$hasIdColumn = false;
foreach ($therapiesCols as $c) {
    if (($c['Field'] ?? '') === 'id') {
        $hasIdColumn = true;
        break;
    }
}

if (!$hasIdColumn) {
    echo "Adding 'id' column to therapies table...\n";
    $pdo->exec('ALTER TABLE therapies ADD COLUMN id INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE FIRST');
    echo "  ✓ ID column added.\n\n";
}

// Check if therapies have IDs
$therapies = $pdo->query('SELECT id, slug, title FROM therapies ORDER BY id')->fetchAll();
$count = count($therapies);

echo "Found $count therapy(s) in database.\n";

$needFix = false;
foreach ($therapies as $t) {
    if ((int) $t['id'] === 0 || $t['id'] === null) {
        $needFix = true;
        break;
    }
}

if (!$needFix && $count > 0) {
    echo "\nAll therapies already have valid IDs. No fix needed.\n";
    exit(0);
}

echo "\nRe-indexing therapy IDs...\n";

// Get all therapies ordered by slug for consistent ordering
$therapies = $pdo->query('SELECT slug, title FROM therapies ORDER BY slug')->fetchAll();

$i = 1;
$updated = 0;
foreach ($therapies as $t) {
    $slug = $t['slug'];
    $title = $t['title'];
    
    // Update the ID to be sequential
    $pdo->prepare('UPDATE therapies SET id = ? WHERE slug = ?')->execute([$i, $slug]);
    
    echo "  ✓ $title (slug: $slug) → ID: $i\n";
    $i++;
    $updated++;
}

// Reset auto-increment to continue from where we left off
$pdo->prepare('ALTER TABLE therapies AUTO_INCREMENT = ' . ($i))->execute();

echo "\n✓ Fixed $updated therapy ID(s).\n";
echo "✓ Auto-increment reset to " . ($i) . "\n";
echo "\nTherapies can now be edited and deleted from the admin panel.\n";
