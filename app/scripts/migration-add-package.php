<?php
/**
 * Migration: Add `package` column to `qr_payments` table.
 *
 * Run once from the project root:
 *   php app/scripts/migration-add-package.php
 *
 * Safe to re-run — uses ADD COLUMN IF NOT EXISTS equivalent.
 */

require __DIR__ . '/../../app/config/config.php';
require APP_ROOT . '/app/core/Database.php';

$pdo = Database::pdo();

if ($pdo === null) {
    echo "⚠  Database not available — skipping migration.\n";
    echo "   The package field will be stored in the JSON file fallback.\n";
    exit(0);
}

try {
    // Check if column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM qr_payments LIKE 'package'");
    if ($stmt->fetch()) {
        echo "✅ Column 'package' already exists — no migration needed.\n";
        exit(0);
    }

    // Add the column after `address` for a logical field order
    $pdo->exec("ALTER TABLE qr_payments ADD COLUMN package VARCHAR(120) NOT NULL DEFAULT '' AFTER address");
    echo "✅ Added 'package' column to qr_payments table.\n";
} catch (Throwable $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
