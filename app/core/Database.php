<?php
/**
 * Lightweight PDO helper. Connections are lazy and never throw:
 * if MySQL is unavailable, Database::pdo() returns null so callers
 * can fall back to file-based storage gracefully.
 */
class Database
{
    private static ?PDO $pdo = null;
    private static bool $attempted = false;

    public static function pdo(): ?PDO
    {
        if (self::$attempted) {
            return self::$pdo;
        }
        self::$attempted = true;

        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (Throwable $e) {
            error_log('[Database] Connection failed: ' . $e->getMessage());
            self::$pdo = null;
        }

        return self::$pdo;
    }
}
