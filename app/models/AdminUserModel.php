<?php
/**
 * Admin panel users (email + bcrypt password hash + role).
 *
 * Backed by the `admin_users` table. If the database is unavailable,
 * operations fall back to the JSON file (storage/admin_users.json)
 * so user management always works.
 *
 * Roles:
 *   'admin' — full access to everything (bookings, content, pages, settings)
 *   'staff' — bookings only (dashboard, appointments, QR payments)
 */
class AdminUserModel
{
    private const TABLE = 'admin_users';

    /* ------------------------------------------------------------------ */
    /* Public API (same for DB and file backends)                         */
    /* ------------------------------------------------------------------ */

    /**
     * @return array<int, array<string, mixed>> Never includes password hashes.
     */
    public static function all(): array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $rows = $pdo->query(
                    'SELECT id, email, username, display_name, role, created_at, updated_at
                     FROM ' . self::TABLE . ' ORDER BY username, id'
                )->fetchAll();

                return array_map(static fn (array $r): array => [
                    'id' => (int) $r['id'],
                    'email' => (string) ($r['email'] ?? ''),
                    'username' => (string) $r['username'],
                    'display_name' => (string) ($r['display_name'] ?? ''),
                    'role' => (string) ($r['role'] ?? 'admin'),
                    'created_at' => (string) ($r['created_at'] ?? ''),
                    'updated_at' => (string) ($r['updated_at'] ?? ''),
                ], $rows);
            } catch (Throwable $e) {
                error_log('[AdminUserModel] DB all() failed: ' . $e->getMessage());
            }
        }

        return self::allFromFile();
    }

    /**
     * @return array<string, mixed>|null raw row including password_hash
     */
    public static function find(int $id): ?array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('SELECT * FROM ' . self::TABLE . ' WHERE id = ?');
                $stmt->execute([$id]);
                $row = $stmt->fetch();
                if ($row !== false) {
                    return $row;
                }
            } catch (Throwable $e) {
                error_log('[AdminUserModel] DB find() failed: ' . $e->getMessage());
            }
        }

        return self::findFromFile($id);
    }

    /**
     * @return array<string, mixed>|null raw row including password_hash
     */
    public static function findByUsername(string $username): ?array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('SELECT * FROM ' . self::TABLE . ' WHERE username = ?');
                $stmt->execute([$username]);
                $row = $stmt->fetch();
                if ($row !== false) {
                    return $row;
                }
            } catch (Throwable $e) {
                error_log('[AdminUserModel] DB findByUsername() failed: ' . $e->getMessage());
            }
        }

        return self::findByUsernameFromFile($username);
    }

    /**
     * @return array<string, mixed>|null raw row including password_hash
     */
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('SELECT * FROM ' . self::TABLE . ' WHERE email = ?');
                $stmt->execute([$email]);
                $row = $stmt->fetch();
                if ($row !== false) {
                    return $row;
                }
            } catch (Throwable $e) {
                error_log('[AdminUserModel] DB findByEmail() failed: ' . $e->getMessage());
            }
        }

        // Always check file fallback (DB might have the table but missing columns)
        $fileUser = self::findByEmailFromFile($email);
        if ($fileUser !== null) {
            return $fileUser;
        }

        // Last resort: search by username too (email might be stored as username)
        return self::findByUsernameFromFile($email);
    }

    /**
     * @return int|false new user id, or false on failure
     */
    public static function create(string $email, string $username, string $passwordHash, string $displayName = '', string $role = 'admin')
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO ' . self::TABLE . ' (email, username, password_hash, display_name, role, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, NOW(), NOW())'
                );
                $stmt->execute([$email, $username, $passwordHash, $displayName, $role]);

                return (int) $pdo->lastInsertId();
            } catch (Throwable $e) {
                error_log('[AdminUserModel] DB create() failed: ' . $e->getMessage());
            }
        }

        return self::createInFile($email, $username, $passwordHash, $displayName, $role);
    }

    /**
     * @param string|null $passwordHash null keeps the current password
     */
    public static function update(int $id, string $email, string $username, ?string $passwordHash, string $displayName = '', string $role = 'admin'): bool
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                if ($passwordHash !== null) {
                    $stmt = $pdo->prepare(
                        'UPDATE ' . self::TABLE . '
                         SET email = ?, username = ?, password_hash = ?, display_name = ?, role = ?, updated_at = NOW()
                         WHERE id = ?'
                    );
                    $stmt->execute([$email, $username, $passwordHash, $displayName, $role, $id]);
                } else {
                    $stmt = $pdo->prepare(
                        'UPDATE ' . self::TABLE . '
                         SET email = ?, username = ?, display_name = ?, role = ?, updated_at = NOW()
                         WHERE id = ?'
                    );
                    $stmt->execute([$email, $username, $displayName, $role, $id]);
                }

                return true;
            } catch (Throwable $e) {
                error_log('[AdminUserModel] DB update() failed: ' . $e->getMessage());
            }
        }

        return self::updateInFile($id, $email, $username, $passwordHash, $displayName, $role);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('DELETE FROM ' . self::TABLE . ' WHERE id = ?');
                $stmt->execute([$id]);

                return $stmt->rowCount() > 0;
            } catch (Throwable $e) {
                error_log('[AdminUserModel] DB delete() failed: ' . $e->getMessage());
            }
        }

        return self::deleteFromFile($id);
    }

    /* ------------------------------------------------------------------ */
    /* File fallback (storage/admin_users.json)                           */
    /* ------------------------------------------------------------------ */

    private static function filePath(): string
    {
        return APP_ROOT . '/storage/admin_users.json';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function allFromFile(): array
    {
        $users = self::readFile();
        // Strip password hashes from the list view
        return array_map(static function (array $u) {
            unset($u['password_hash']);
            return $u;
        }, $users);
    }

    private static function findFromFile(int $id): ?array
    {
        foreach (self::readFile() as $u) {
            if ((int) ($u['id'] ?? 0) === $id) {
                return $u;
            }
        }
        return null;
    }

    private static function findByUsernameFromFile(string $username): ?array
    {
        foreach (self::readFile() as $u) {
            if (strtolower((string) ($u['username'] ?? '')) === strtolower($username)) {
                return $u;
            }
        }
        return null;
    }

    private static function findByEmailFromFile(string $email): ?array
    {
        foreach (self::readFile() as $u) {
            if (strtolower((string) ($u['email'] ?? '')) === strtolower($email)) {
                return $u;
            }
        }
        return null;
    }

    private static function createInFile(string $email, string $username, string $passwordHash, string $displayName, string $role)
    {
        $users = self::readFile();

        // Generate a unique numeric ID
        $maxId = 0;
        foreach ($users as $u) {
            $maxId = max($maxId, (int) ($u['id'] ?? 0));
        }

        $now = date('Y-m-d H:i:s');
        $newUser = [
            'id' => $maxId + 1,
            'email' => $email,
            'username' => $username,
            'password_hash' => $passwordHash,
            'display_name' => $displayName,
            'role' => $role,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $users[] = $newUser;
        self::writeFile($users);

        error_log('[AdminUserModel] Created user in file: ' . $email . ' (id=' . $newUser['id'] . ')');

        return $newUser['id'];
    }

    private static function updateInFile(int $id, string $email, string $username, ?string $passwordHash, string $displayName, string $role): bool
    {
        $users = self::readFile();
        $found = false;

        foreach ($users as &$u) {
            if ((int) ($u['id'] ?? 0) === $id) {
                $u['email'] = $email;
                $u['username'] = $username;
                $u['display_name'] = $displayName;
                $u['role'] = $role;
                $u['updated_at'] = date('Y-m-d H:i:s');
                if ($passwordHash !== null) {
                    $u['password_hash'] = $passwordHash;
                }
                $found = true;
                break;
            }
        }
        unset($u);

        if ($found) {
            self::writeFile($users);
        }

        return $found;
    }

    private static function deleteFromFile(int $id): bool
    {
        $users = self::readFile();
        $found = false;

        foreach ($users as $i => $u) {
            if ((int) ($u['id'] ?? 0) === $id) {
                unset($users[$i]);
                $found = true;
                break;
            }
        }

        if ($found) {
            self::writeFile(array_values($users));
        }

        return $found;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function readFile(): array
    {
        $file = self::filePath();
        if (!is_file($file)) {
            error_log('[AdminUserModel] readFile: file does not exist: ' . $file);
            return [];
        }

        $raw = @file_get_contents($file);
        if ($raw === false || $raw === '') {
            error_log('[AdminUserModel] readFile: file_get_contents failed or empty: ' . $file);
            return [];
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            error_log('[AdminUserModel] readFile: json_decode failed for: ' . $file);
            return [];
        }

        error_log('[AdminUserModel] readFile: loaded ' . count($data) . ' users from ' . $file);
        return $data;
    }

    /**
     * @param array<int, array<string, mixed>> $users
     */
    private static function writeFile(array $users): void
    {
        $file = self::filePath();
        $json = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $bytes = file_put_contents($file, $json, LOCK_EX);
        if ($bytes === false) {
            error_log('[AdminUserModel] writeFile FAILED: ' . $file . ' — check permissions');
        } else {
            error_log('[AdminUserModel] writeFile OK: ' . $bytes . ' bytes to ' . $file);
        }
    }
}
