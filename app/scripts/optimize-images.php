<?php
/**
 * Batch-convert all uploaded images to WebP.
 *
 * Run from the project root:
 *   php app/scripts/optimize-images.php
 *
 * This scans public/uploads/ and converts every JPG, PNG, and GIF
 * to WebP, then deletes the originals to free disk space.
 *
 * Safe to re-run — already-converted files are skipped.
 */

require __DIR__ . '/../../app/config/config.php';
require APP_ROOT . '/app/core/ImageOptimizer.php';

$directories = [
    APP_ROOT . '/public/uploads',
];

echo "🖼  Chitrawan Nature Cure Hospital — Image Optimizer\n";
echo str_repeat('─', 50) . "\n\n";

$totalConverted = 0;
$totalSkipped = 0;
$totalFailed = 0;

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        echo "⚠  Directory not found: $dir\n";
        continue;
    }

    echo "Scanning: $dir\n";
    $stats = ImageOptimizer::batchConvert($dir);

    $totalConverted += $stats['converted'];
    $totalSkipped += $stats['skipped'];
    $totalFailed += $stats['failed'];

    echo "  ✓ Converted: {$stats['converted']}\n";
    echo "  ⊘ Skipped:   {$stats['skipped']} (already WebP or unsupported)\n";
    if ($stats['failed'] > 0) {
        echo "  ✗ Failed:    {$stats['failed']}\n";
    }
    echo "\n";
}

echo str_repeat('─', 50) . "\n";
echo "Total: {$totalConverted} converted, {$totalSkipped} skipped, {$totalFailed} failed\n";

if ($totalConverted > 0) {
    echo "\n✅ Done! Original images have been replaced with optimized WebP versions.\n";
    echo "   Storage saved: ~30-50% reduction in image file sizes.\n";
} else {
    echo "\n✅ All images are already optimized or no images were found.\n";
}
