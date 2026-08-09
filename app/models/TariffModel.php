<?php
/**
 * Tariff content (plans + service rate card) from the `tariff_plans` and
 * `tariff_services` tables, falling back to the static data file
 * (app/data/tariff.php) when MySQL is unavailable or a table is empty.
 * Each table is resolved independently, so a partial DB state never
 * discards the rows of the other table.
 */
class TariffModel
{
    private const PLANS_TABLE = 'tariff_plans';
    private const SERVICES_TABLE = 'tariff_services';

    /**
     * @return array{plans: array<int, array<string, mixed>>, services: array<int, array<string, mixed>>}
     */
    public static function all(): array
    {
        return ['plans' => self::allPlans(), 'services' => self::allServices()];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function allPlans(bool $fallback = true): array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                // If the table exists, its rows are authoritative — an
                // intentionally empty section stays empty (admins can
                // delete every item without the seed data returning).
                $rows = $pdo->query('SELECT * FROM ' . self::PLANS_TABLE . ' ORDER BY sort_order, id')->fetchAll();

                return array_map(static fn (array $r): array => [
                    'id' => (int) $r['id'],
                    'name' => (string) $r['name'],
                    'description' => (string) ($r['description'] ?? ''),
                    'price' => (string) ($r['price'] ?? ''),
                    'period' => (string) ($r['period'] ?? ''),
                    'features' => self::decodeList($r['features']),
                    'featured' => (bool) $r['featured'],
                    'badge' => (string) ($r['badge'] ?? ''),
                    'ctaLabel' => (string) ($r['cta_label'] ?? 'Book a Session'),
                    'sort_order' => (int) ($r['sort_order'] ?? 0),
                ], $rows);
            } catch (Throwable $e) {
                error_log('[TariffModel] Plans query failed: ' . $e->getMessage());
            }
        }

        // Static data only when MySQL is unreachable or the table is missing.
        if ($fallback) {
            $static = require APP_ROOT . '/app/data/tariff.php';

            return $static['plans'] ?? [];
        }

        return [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function allServices(bool $fallback = true): array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                // If the table exists, its rows are authoritative — an
                // intentionally empty section stays empty (admins can
                // delete every item without the seed data returning).
                $rows = $pdo->query('SELECT * FROM ' . self::SERVICES_TABLE . ' ORDER BY sort_order, id')->fetchAll();

                return array_map(static fn (array $r): array => [
                    'id' => (int) $r['id'],
                    'service' => (string) $r['service'],
                    'duration' => (string) ($r['duration'] ?? ''),
                    'price' => (string) ($r['price'] ?? ''),
                    'sort_order' => (int) ($r['sort_order'] ?? 0),
                ], $rows);
            } catch (Throwable $e) {
                error_log('[TariffModel] Services query failed: ' . $e->getMessage());
            }
        }

        // Static data only when MySQL is unreachable or the table is missing.
        if ($fallback) {
            $static = require APP_ROOT . '/app/data/tariff.php';

            return $static['services'] ?? [];
        }

        return [];
    }

    /**
     * @return array<string, mixed>|null raw row including `id`
     */
    public static function findPlan(int $id): ?array
    {
        return self::find($id, self::PLANS_TABLE);
    }

    /**
     * @return array<string, mixed>|null raw row including `id`
     */
    public static function findService(int $id): ?array
    {
        return self::find($id, self::SERVICES_TABLE);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function savePlan(array $data): bool
    {
        return self::write($data, self::PLANS_TABLE);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function saveService(array $data): bool
    {
        return self::write($data, self::SERVICES_TABLE);
    }

    public static function deletePlan(int $id): bool
    {
        return self::remove($id, self::PLANS_TABLE);
    }

    public static function deleteService(int $id): bool
    {
        return self::remove($id, self::SERVICES_TABLE);
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function find(int $id, string $table): ?array
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return null;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();

            return $row !== false ? $row : null;
        } catch (Throwable $e) {
            error_log("[$table] Find failed: " . $e->getMessage());

            return null;
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function write(array $data, string $table): bool
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
                $pdo->prepare("UPDATE `$table` SET $set WHERE id = ?")->execute($params);

                return true;
            }

            $cols = implode(', ', array_map(static fn (string $c): string => "`$c`", array_keys($data)));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $pdo->prepare("INSERT INTO `$table` ($cols) VALUES ($placeholders)")->execute(array_values($data));

            return true;
        } catch (Throwable $e) {
            error_log("[$table] Save failed: " . $e->getMessage());

            return false;
        }
    }

    private static function remove(int $id, string $table): bool
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return false;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM `$table` WHERE id = ?");
            $stmt->execute([$id]);

            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            error_log("[$table] Delete failed: " . $e->getMessage());

            return false;
        }
    }

    /**
     * @return array<int, mixed>
     */
    private static function decodeList(?string $json): array
    {
        if ($json === null || $json === '') {
            return [];
        }
        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }
}
