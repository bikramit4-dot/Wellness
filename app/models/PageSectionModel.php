<?php
/**
 * Editable page sections from the `page_sections` table, falling back to the
 * static data file (app/data/page_sections.php) when MySQL is unavailable.
 *
 * Each row is one section of one page (e.g. page 'home', section 'hero').
 * Field names are fixed: kicker, heading, content, sub_content, image, media,
 * link, link_label, extras. `extras` stores a JSON array of lines (used for
 * checklists, stats, buttons, doctor cards, ...).
 */
class PageSectionModel
{
    private const TABLE = 'page_sections';

    private static string $lastError = '';

    public static function lastError(): string
    {
        return self::$lastError;
    }

    /**
     * @return array<string, array<string, array<string, mixed>>> keyed [page][section]
     */
    public static function all(bool $fallback = true): array
    {
        $defaults = require APP_ROOT . '/app/data/page_sections.php';

        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $rows = $pdo->query('SELECT * FROM ' . self::TABLE . ' ORDER BY id')->fetchAll();
                $db = self::index($rows);

                return self::mergeDefaults($defaults, $db);
            } catch (Throwable $e) {
                error_log('[PageSectionModel] Query failed: ' . $e->getMessage());
            }
        }

        if ($fallback) {
            return $defaults;
        }

        return [];
    }

    /**
     * Sections for a single page, keyed by section key.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function forPage(string $pageKey, bool $fallback = true): array
    {
        $defaults = require APP_ROOT . '/app/data/page_sections.php';
        $pageDefaults = $defaults[$pageKey] ?? [];

        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('SELECT * FROM ' . self::TABLE . ' WHERE page_key = ? ORDER BY id');
                $stmt->execute([$pageKey]);
                $rows = $stmt->fetchAll();
                $db = self::index($rows)[$pageKey] ?? [];

                return self::mergeDefaults($pageDefaults, $db);
            } catch (Throwable $e) {
                error_log('[PageSectionModel] forPage failed: ' . $e->getMessage());
            }
        }

        if ($fallback) {
            return $pageDefaults;
        }

        return [];
    }

    /**
     * Merge DB rows over the static defaults so every expected field exists
     * (sections deleted by the admin fall back to their default content).
     *
     * @param array<string, array<string, mixed>> $defaults
     * @param array<string, array<string, mixed>> $db
     * @return array<string, array<string, mixed>>
     */
    private static function mergeDefaults(array $defaults, array $db): array
    {
        foreach ($defaults as $sectionKey => $fields) {
            $defaults[$sectionKey] = array_merge($fields, $db[$sectionKey] ?? []);
        }
        foreach ($db as $sectionKey => $fields) {
            if (!isset($defaults[$sectionKey])) {
                $defaults[$sectionKey] = $fields;
            }
        }

        return $defaults;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<string, array<string, array<string, mixed>>>
     */
    private static function index(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            $page = (string) $r['page_key'];
            $section = (string) $r['section_key'];
            $out[$page][$section] = [
                'id' => (int) $r['id'],
                'kicker' => (string) ($r['kicker'] ?? ''),
                'heading' => (string) ($r['heading'] ?? ''),
                'content' => (string) ($r['content'] ?? ''),
                'sub_content' => (string) ($r['sub_content'] ?? ''),
                'image' => (string) ($r['image'] ?? ''),
                'media' => (string) ($r['media'] ?? ''),
                'link' => (string) ($r['link'] ?? ''),
                'link_label' => (string) ($r['link_label'] ?? ''),
                'extras' => self::decodeExtras($r['extras'] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * @return array<int, string>
     */
    public static function decodeExtras(string $raw): array
    {
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($line): string => trim((string) $line),
            $decoded
        )));
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
            error_log('[PageSectionModel] Find failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Insert (no id) or update (id present). If editing and the unique
     * (page_key, section_key) pair is not matched by the given id, fall back
     * to an upsert so re-created sections never duplicate.
     *
     * @param array<string, mixed> $data
     */
    public static function save(array $data): bool
    {
        self::$lastError = '';
        $pdo = Database::pdo();
        if ($pdo === null) {
            self::$lastError = 'Database connection failed. Check DB_HOST, DB_NAME, DB_USER, DB_PASS in your .env file.';
            return false;
        }

        $id = (int) ($data['id'] ?? 0);
        unset($data['id']);
        // `extras` may arrive as a decoded array (seed defaults) or as an
        // already-encoded JSON string (admin form). Never double-encode.
        if (is_array($data['extras'] ?? null)) {
            $data['extras'] = json_encode(
                $data['extras'],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        } else {
            $data['extras'] = (string) ($data['extras'] ?? '');
        }

        $cols = array_keys($data);
        $values = array_values($data);

        try {
            if ($id > 0) {
                // Does this id still exist? If not, do an upsert instead.
                $check = $pdo->prepare('SELECT id FROM ' . self::TABLE . ' WHERE id = ?');
                $check->execute([$id]);
                if ($check->fetch() !== false) {
                    $set = implode(', ', array_map(static fn (string $c): string => "`$c` = ?", $cols));
                    $params = $values;
                    $params[] = $id;
                    $pdo->prepare('UPDATE ' . self::TABLE . " SET $set WHERE id = ?")->execute($params);

                    return true;
                }
            }

            $placeholders = implode(', ', array_fill(0, count($cols), '?'));
            $sql = 'INSERT INTO ' . self::TABLE . ' (`' . implode('`, `', $cols) . '`) VALUES (' . $placeholders . ')
                    ON DUPLICATE KEY UPDATE '
                . implode(', ', array_map(static fn (string $c): string => "`$c` = VALUES(`$c`)", $cols));
            $pdo->prepare($sql)->execute($values);

            return true;
        } catch (Throwable $e) {
            self::$lastError = $e->getMessage();
            error_log('[PageSectionModel] Save failed: ' . $e->getMessage());

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
            error_log('[PageSectionModel] Delete failed: ' . $e->getMessage());

            return false;
        }
    }
}
