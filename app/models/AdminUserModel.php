<?php
/**
 * Admin panel users (username + bcrypt password hash).
 *
 * Backed by the `admin_users` table, created/updated from the admin panel
 * (Admin → Users). The original single admin in app/config/config.php
 * (ADMIN_USERNAME / ADMIN_PASSWORD_HASH) continues to work as a fallback.
 */
class AdminUserModel
{
    private const TABLE = 'admin_users';

    /**
     * @return array<int, array<string, mixed>> Never includes password hashes.
     */
    public static function all(): array
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return [];
        }

        try {
            $rows = $pdo->query(
                'SELECT id, username, display_name, created_at, updated_at
                 FROM ' . self::TABLE . ' ORDER BY username, id'
            )->fetchAll();

            return array_map(static fn (array $r): array => [
                'id' => (int) $r['id'],
                'username' => (string) $r['username'],
                'display_name' => (string) ($r['display_name'] ?? ''),
                'created_at' => (string) ($r['created_at'] ?? ''),
                'updated_at' => (string) ($r['updated_at'] ?? ''),
            ], $rows);
        } catch (Throwable $e) {
            error_log('[AdminUserModel] All failed: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * @return array<string, mixed>|null raw row including password_hash
     */
    public static function find(int $id): ?array
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return null;
        }

        try {
            $stmt = $pdo->prepare('SELECT * FROM ' . self::TABLE . ' WHERE id = ?');
            $stmt->execute([$id]);
            $row = $stmt->fetch();

            return $row !== false ? $row : null;
        } catch (Throwable $e) {
            error_log('[AdminUserModel] Find failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * @return array<string, mixed>|null raw row including password_hash
     */
    public static function findByUsername(string $username): ?array
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return null;
        }

        try {
            $stmt = $pdo->prepare('SELECT * FROM ' . self::TABLE . ' WHERE username = ?');
            $stmt->execute([$username]);
            $row = $stmt->fetch();

            return $row !== false ? $row : null;
        } catch (Throwable $e) {
            error_log('[AdminUserModel] FindByUsername failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * @return int|false new user id, or false on failure
     */
    public static function create(string $username, string $passwordHash, string $displayName = '')
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return false;
        }

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO ' . self::TABLE . ' (username, password_hash, display_name, created_at, updated_at)
                 VALUES (?, ?, ?, NOW(), NOW())'
            );
            $stmt->execute([$username, $passwordHash, $displayName]);

            return (int) $pdo->lastInsertId();
        } catch (Throwable $e) {
            error_log('[AdminUserModel] Create failed: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * @param string|null $passwordHash null keeps the current password
     */
    public static function update(int $id, string $username, ?string $passwordHash, string $displayName = ''): bool
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return false;
        }

        try {
            if ($passwordHash !== null) {
                $stmt = $pdo->prepare(
                    'UPDATE ' . self::TABLE . '
                     SET username = ?, password_hash = ?, display_name = ?, updated_at = NOW()
                     WHERE id = ?'
                );
                $stmt->execute([$username, $passwordHash, $displayName, $id]);
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE ' . self::TABLE . '
                     SET username = ?, display_name = ?, updated_at = NOW()
                     WHERE id = ?'
                );
                $stmt->execute([$username, $displayName, $id]);
            }

            return true;
        } catch (Throwable $e) {
            error_log('[AdminUserModel] Update failed: ' . $e->getMessage());

            return false;
        }
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return false;
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM ' . self::TABLE . ' WHERE id = ?');
            $stmt->execute([$id]);

            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            error_log('[AdminUserModel] Delete failed: ' . $e->getMessage());

            return false;
        }
    }
}
