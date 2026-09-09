<?php
/**
 * Migration: Add `status` column to `testimonials` table.
 *
 * Run once: php app/scripts/migration-add-review-status.php
 *
 * Existing testimonials are set to 'approved' so they remain visible.
 */

require __DIR__ . '/../../app/config/config.php';
require APP_ROOT . '/app/core/Database.php';

$pdo = Database::pdo();

if ($pdo === null) {
    echo "⚠  Database not available — skipping migration.\n";
    exit(0);
}

try {
    // Check if column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM testimonials LIKE 'status'");
    if ($stmt->fetch()) {
        echo "✅ Column 'status' already exists — no migration needed.\n";
        exit(0);
    }

    // Add the column
    $pdo->exec("ALTER TABLE testimonials ADD COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved' AFTER quote");
    echo "✅ Added 'status' column to testimonials table.\n";

    // Mark all existing testimonials as approved (they were already visible)
    $pdo->exec("UPDATE testimonials SET status = 'approved' WHERE status = 'pending'");
    echo "✅ Marked existing testimonials as 'approved'.\n";
} catch (Throwable $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
