<?php
/**
 * Update all "Harmony" references to "Chitrawan Nature Cure Hospital" in the database.
 * 
 * Usage: php app/scripts/update-brand.php
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

echo "Updating database branding from 'Harmony' to 'Chitrawan Nature Cure Hospital'...\n\n";

// Update page_sections table - replace all Harmony references
$updates = [
    // Brand name
    ['page_key = "site" AND section_key = "brand"', 'heading', 'Harmony Wellness', 'Chitrawan Nature Cure Hospital'],
    
    // Home hero
    ['page_key = "home" AND section_key = "hero"', 'content', 'Harmony Wellness Center', 'Chitrawan Nature Cure Hospital'],
    
    // Home about_intro
    ['page_key = "home" AND section_key = "about_intro"', 'kicker', 'About Hremovearmony', 'About Chitrawan'],
    ['page_key = "home" AND section_key = "about_intro"', 'content', 'Harmony Wellness Center', 'Chitrawan Nature Cure Hospital'],
    
    // Home story
    ['page_key = "home" AND section_key = "story"', 'content', 'Harmony Wellness Center', 'Chitrawan Nature Cure Hospital'],
    
    // About hero
    ['page_key = "about" AND section_key = "hero"', 'content', 'Harmony Wellness Center', 'Chitrawan Nature Cure Hospital'],
    
    // About founder
    ['page_key = "about" AND section_key = "founder"', 'content', 'Harmony Wellness Center', 'Chitrawan Nature Cure Hospital'],
    
    // About group
    ['page_key = "about" AND section_key = "group"', 'content', 'Harmony Wellness Center', 'Chitrawan Nature Cure Hospital'],
    
    // Contact info
    ['page_key = "contact" AND section_key = "info"', 'extras', NULL, NULL], // Special handling below
    
    // Contact map
    ['page_key = "contact" AND section_key = "map"', 'content', 'Harmony Wellness Center', 'Chitrawan Nature Cure Hospital'],
    ['page_key = "contact" AND section_key = "map"', 'link', 'Harmony Wellness Center', 'Chitrawan Nature Cure Hospital'],
];

$count = 0;
foreach ($updates as $update) {
    [$where, $column, $oldVal, $newVal] = $update;
    
    if ($oldVal !== null && $newVal !== null) {
        $sql = "UPDATE page_sections SET $column = REPLACE($column, ?, ?) WHERE $where";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$oldVal, $newVal]);
        $affected = $stmt->rowCount();
        if ($affected > 0) {
            echo "  ✓ Updated $column in $where: $oldVal → $newVal ($affected row(s))\n";
            $count += $affected;
        }
    }
}

// Special handling for contact info extras (JSON array)
$sql = "SELECT id, extras FROM page_sections WHERE page_key = 'contact' AND section_key = 'info'";
$stmt = $pdo->query($sql);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && $row['extras']) {
    $extras = json_decode($row['extras'], true);
    $changed = false;
    
    foreach ($extras as &$item) {
        if (strpos($item, '|') !== false) {
            [$label, $value] = explode('|', $item, 2);
            
            if (strpos($value, 'Harmony Wellness Center') !== false) {
                $value = str_replace('Harmony Wellness Center', 'Chitrawan Nature Cure Hospital', $value);
                $item = "$label|$value";
                $changed = true;
            }
            
            if (strpos($value, 'info@harmonywellness.com') !== false) {
                $value = str_replace('info@harmonywellness.com', 'nchchitwan@gmail.com', $value);
                $item = "$label|$value";
                $changed = true;
            }
            
            if (strpos($value, '+977-9800000000') !== false) {
                $value = str_replace('+977-9800000000', '+977 56-535213', $value);
                $item = "$label|$value";
                $changed = true;
            }
        }
    }
    
    if ($changed) {
        $newExtras = json_encode($extras, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $pdo->prepare("UPDATE page_sections SET extras = ? WHERE page_key = 'contact' AND section_key = 'info'")
            ->execute([$newExtras]);
        echo "  ✓ Updated contact info extras with new email/phone/address\n";
        $count++;
    }
}

// Update any remaining content fields that might have Harmony references
$sql = "SELECT id, page_key, section_key, content FROM page_sections WHERE content LIKE '%Harmony%'";
$stmt = $pdo->query($sql);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $newContent = str_replace('Harmony Wellness Center', 'Chitrawan Nature Cure Hospital', $row['content']);
    $newContent = str_replace('Harmony Wellness', 'Chitrawan Nature Cure Hospital', $newContent);
    
    if ($newContent !== $row['content']) {
        $pdo->prepare("UPDATE page_sections SET content = ? WHERE id = ?")
            ->execute([$newContent, $row['id']]);
        echo "  ✓ Updated content in {$row['page_key']}/{$row['section_key']}\n";
        $count++;
    }
}

echo "\nTotal updates: $count\n";
echo "\nBanner branding should now show: Chitrawan Nature Cure Hospital\n";
echo "Contact email: nchchitwan@gmail.com\n";
echo "Contact phone: +977 56-535213\n";
