<?php
/**
 * Patient testimonials from the `testimonials` table, falling back to the
 * static data file (app/data/testimonials.php) when MySQL is unavailable
 * or the table is empty.
 */
class TestimonialModel
{
    private const TABLE = 'testimonials';

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(bool $fallback = true): array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                // Public view: only show approved testimonials.
                $rows = $pdo->query('SELECT * FROM ' . self::TABLE . " WHERE status = 'approved' ORDER BY sort_order, id")->fetchAll();

                return array_map(static fn (array $r): array => [
                    'id' => (int) $r['id'],
                    'name' => (string) $r['name'],
                    'role' => (string) ($r['role'] ?? ''),
                    'initials' => (string) ($r['initials'] ?? ''),
                    'avatar' => (string) ($r['avatar'] ?? 'a1'),
                    'rating' => (int) ($r['rating'] ?? 5),
                    'quote' => (string) ($r['quote'] ?? ''),
                    'status' => (string) ($r['status'] ?? 'approved'),
                    'sort_order' => (int) ($r['sort_order'] ?? 0),
                ], $rows);
            } catch (Throwable $e) {
                error_log('[TestimonialModel] Query failed: ' . $e->getMessage());
            }
        }

        // Static data only when MySQL is unreachable or the table is missing.
        if ($fallback) {
            return require APP_ROOT . '/app/data/testimonials.php';
        }

        return [];
    }

    /**
     * Admin view: return all testimonials regardless of status.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function allAdmin(): array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $rows = $pdo->query('SELECT * FROM ' . self::TABLE . ' ORDER BY status ASC, sort_order, id DESC')->fetchAll();

                return array_map(static fn (array $r): array => [
                    'id' => (int) $r['id'],
                    'name' => (string) $r['name'],
                    'role' => (string) ($r['role'] ?? ''),
                    'initials' => (string) ($r['initials'] ?? ''),
                    'avatar' => (string) ($r['avatar'] ?? 'a1'),
                    'rating' => (int) ($r['rating'] ?? 5),
                    'quote' => (string) ($r['quote'] ?? ''),
                    'status' => (string) ($r['status'] ?? 'pending'),
                    'sort_order' => (int) ($r['sort_order'] ?? 0),
                ], $rows);
            } catch (Throwable $e) {
                error_log('[TestimonialModel] allAdmin failed: ' . $e->getMessage());
            }
        }

        return self::all(false);
    }

    /**
     * Update the status of a testimonial (approve / reject).
     */
    public static function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            return false;
        }

        $pdo = Database::pdo();
        if ($pdo === null) {
            return false;
        }

        try {
            $stmt = $pdo->prepare('UPDATE ' . self::TABLE . ' SET status = ? WHERE id = ?');
            $stmt->execute([$status, $id]);

            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            error_log('[TestimonialModel] updateStatus failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Count testimonials by status.
     *
     * @return array{pending: int, approved: int, rejected: int, total: int}
     */
    public static function statusCounts(): array
    {
        $counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0];

        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $rows = $pdo->query('SELECT status, COUNT(*) AS cnt FROM ' . self::TABLE . ' GROUP BY status')->fetchAll();
                foreach ($rows as $r) {
                    $s = (string) $r['status'];
                    $counts[$s] = (int) $r['cnt'];
                    $counts['total'] += (int) $r['cnt'];
                }
            } catch (Throwable $e) {
                error_log('[TestimonialModel] statusCounts failed: ' . $e->getMessage());
            }
        }

        return $counts;
    }

    /**
     * @return array<string, mixed>|null raw row including `id`
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
            error_log('[TestimonialModel] Find failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Insert (no id) or update (id present). Column names come from the
     * admin form config, never from user input.
     *
     * @param array<string, mixed> $data
     */
    public static function save(array $data): bool
    {
        return self::write($data);
    }

    public static function delete(int $id): bool
    {
        return self::remove($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function write(array $data): bool
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return false;
        }

        $id = (int) ($data['id'] ?? 0);
        unset($data['id']);

        try {
            if ($id > 0) {
                $set = implode(', ', array_map(static fn (string $c): string => "`$c` = ?", array_keys($data)));
                $params = array_values($data);
                $params[] = $id;
                $pdo->prepare('UPDATE ' . self::TABLE . " SET $set WHERE id = ?")->execute($params);

                return true;
            }

            $cols = implode(', ', array_map(static fn (string $c): string => "`$c`", array_keys($data)));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $pdo->prepare('INSERT INTO ' . self::TABLE . " ($cols) VALUES ($placeholders)")->execute(array_values($data));

            return true;
        } catch (Throwable $e) {
            error_log('[' . self::TABLE . '] Save failed: ' . $e->getMessage());

            return false;
        }
    }

    private static function remove(int $id): bool
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
            error_log('[' . self::TABLE . '] Delete failed: ' . $e->getMessage());

            return false;
        }
    }
}
