<?php
/**
 * Migration: allow appointments to be rejected.
 *
 * Run once from the project root:
 *   php app/scripts/migration-add-appointment-rejected-status.php
 */

require_once __DIR__ . '/../core/Dotenv.php';
Dotenv::load(dirname(__DIR__, 2) . '/.env');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

$pdo = Database::pdo();
if ($pdo === null) {
    fwrite(STDERR, "Database connection failed.\n");
    exit(1);
}

try {
    $pdo->exec("ALTER TABLE appointments MODIFY status ENUM('new', 'confirmed', 'rejected', 'completed') NOT NULL DEFAULT 'new'");
    echo "Appointment status enum updated.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Migration failed: " . $e->getMessage() . "\n");
    exit(1);
}