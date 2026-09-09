<?php
/**
 * Loads therapy pages from the `therapies` table, falling back to the
 * static data file (app/data/therapies.php) when MySQL is unavailable.
 */
class TherapyModel
{
    private const CATEGORY_LABELS = [
        '/treatments' => 'Treatments',
        '/physiotherapy' => 'Physiotherapy',
        '/diet-therapy' => 'Diet Therapy',
        '/special-therapies' => 'Special Therapies',
    ];

    /**
     * @return array<string, array<string, mixed>> keyed by slug
     */
    public static function all(): array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $rows = $pdo->query('SELECT * FROM therapies ORDER BY title')->fetchAll();
                $out = [];
                foreach ($rows as $r) {
                    $out[$r['slug']] = self::rowToEntry($r);
                }
                if ($out !== []) {
                    return $out;
                }
            } catch (Throwable $e) {
                error_log('[TherapyModel] Query failed: ' . $e->getMessage());
            }
        }

        return require APP_ROOT . '/app/data/therapies.php';
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function find(string $slug): ?array
    {
        $therapies = self::all();
        return $therapies[$slug] ?? null;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private static function rowToEntry(array $row): array
    {
        $category = (string) $row['category'];

        return [
            'title' => $row['title'],
            'category' => $category,
            'categoryLabel' => self::CATEGORY_LABELS[$category] ?? 'Therapies',
            'icon' => $row['icon'] ?: 'icon-leaf',
            'image' => $row['image'] ?? '',
            'intro' => $row['intro'] ?? '',
            'about' => self::decodeList($row['about']),
            'methods' => self::decodeList($row['methods']),
            'benefits' => self::decodeList($row['benefits']),
        ];
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

    /* ------------------------------------------------------------------ */
    /* Admin content manager (numeric id based)                           */
    /* ------------------------------------------------------------------ */

    /**
     * Admin listing: one entry per therapy with a numeric `id`, decoded
     * content fields, category label and a technique count.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function allAdmin(bool $fallback = true): array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $rows = $pdo->query('SELECT * FROM therapies ORDER BY category, title')->fetchAll();
                $out = [];
                foreach ($rows as $r) {
                    $out[] = self::rowToAdmin($r);
                }

                return $out;
            } catch (Throwable $e) {
                error_log('[TherapyModel] allAdmin failed: ' . $e->getMessage());
            }
        }

        // Data-file fallback with synthetic ids (only when MySQL is down).
        if ($fallback) {
            $out = [];
            $i = 0;
            foreach (require APP_ROOT . '/app/data/therapies.php' as $slug => $t) {
                $out[] = self::rowToAdmin([
                    'id' => 100000 + (++$i),
                    'slug' => $slug,
                    'category' => $t['category'] ?? '',
                    'title' => $t['title'] ?? '',
                    'icon' => $t['icon'] ?? 'icon-leaf',
                    'image' => $t['image'] ?? '',
                    'intro' => $t['intro'] ?? '',
                    'about' => $t['about'] ?? [],
                    'methods' => $t['methods'] ?? [],
                    'benefits' => $t['benefits'] ?? [],
                ]);
            }

            return $out;
        }

        return [];
    }

    /**
     * @return array<string, mixed>|null decoded admin entry including `id`
     */
    public static function findAdmin(int $id): ?array
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return null;
        }

        try {
            $stmt = $pdo->prepare('SELECT * FROM therapies WHERE id = ?');
            $stmt->execute([$id]);
            $row = $stmt->fetch();

            return $row !== false ? self::rowToAdmin($row) : null;
        } catch (Throwable $e) {
            error_log('[TherapyModel] findAdmin failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Insert (no id) or update (id present). The slug (page URL) is fixed on
     * update — renaming a therapy never breaks its existing links.
     *
     * @param array<string, mixed> $data
     */
    public static function saveAdmin(array $data): bool
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return false;
        }

        $id = (int) ($data['id'] ?? 0);
        unset($data['id'], $data['categoryLabel'], $data['methodsCount']);

        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            return false;
        }
        $data['title'] = $title;
        $data['category'] = (string) ($data['category'] ?? '/treatments');
        $data['icon'] = (string) ($data['icon'] ?? 'icon-leaf');
        $data['image'] = (string) ($data['image'] ?? '');
        $data['intro'] = (string) ($data['intro'] ?? '');
        $data['about'] = self::asJson($data['about'] ?? []);
        $data['methods'] = self::asJson(self::normalizeMethods($data['methods'] ?? []));
        $data['benefits'] = self::asJson($data['benefits'] ?? []);

        try {
            if ($id > 0) {
                $set = implode(', ', array_map(static fn (string $c): string => "`$c` = ?", array_keys($data)));
                $params = array_values($data);
                $params[] = $id;
                $pdo->prepare("UPDATE therapies SET $set WHERE id = ?")->execute($params);

                return true;
            }

            // New therapy: generate a unique slug from the title.
            $slug = self::slugify($title);
            if ($slug === '') {
                return false;
            }
            $check = $pdo->prepare('SELECT COUNT(*) FROM therapies WHERE slug = ?');
            $base = $slug;
            $n = 1;
            do {
                $check->execute([$slug]);
                if ((int) $check->fetchColumn() === 0) {
                    break;
                }
                $slug = $base . '-' . (++$n);
            } while ($n < 50);

            $data['slug'] = $slug;
            $cols = implode(', ', array_map(static fn (string $c): string => "`$c`", array_keys($data)));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $pdo->prepare("INSERT INTO therapies ($cols) VALUES ($placeholders)")->execute(array_values($data));

            return true;
        } catch (Throwable $e) {
            error_log('[TherapyModel] saveAdmin failed: ' . $e->getMessage());

            return false;
        }
    }

    public static function deleteAdmin(int $id): bool
    {
        $pdo = Database::pdo();
        if ($pdo === null) {
            return false;
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM therapies WHERE id = ?');
            $stmt->execute([$id]);

            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            error_log('[TherapyModel] deleteAdmin failed: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Convert stored methods (plain strings or title+definition arrays) into
     * "Title | Definition" lines for the admin textarea.
     *
     * @return array<int, string>
     */
    public static function methodsToLines(mixed $methods): array
    {
        $lines = [];
        $items = is_array($methods) ? $methods : (json_decode((string) $methods, true) ?: []);
        foreach ($items as $m) {
            if (is_string($m) && trim($m) !== '') {
                $lines[] = trim($m);
            } elseif (is_array($m)) {
                $title = trim((string) ($m['title'] ?? ''));
                if ($title === '') {
                    continue;
                }
                $def = trim((string) ($m['definition'] ?? ''));
                $lines[] = $def !== '' ? $title . ' | ' . $def : $title;
            }
        }

        return $lines;
    }

    /**
     * Parse textarea lines ("Title | Definition") back into stored methods.
     * Lines without a definition stay plain strings.
     *
     * @return array<int, mixed>
     */
    public static function methodsFromLines(mixed $raw): array
    {
        $lines = is_array($raw) ? $raw : preg_split('/\r\n|\r|\n/', (string) $raw);
        $out = [];
        foreach ((array) $lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            $parts = array_map('trim', explode('|', $line, 2));
            $title = $parts[0];
            $def = $parts[1] ?? '';
            $out[] = $def !== '' ? ['title' => $title, 'definition' => $def] : $title;
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $r raw DB row (or data-file entry)
     * @return array<string, mixed>
     */
    private static function rowToAdmin(array $r): array
    {
        $methods = self::decodeList($r['methods'] ?? null);
        $category = (string) ($r['category'] ?? '');

        return [
            'id' => (int) ($r['id'] ?? 0),
            'slug' => (string) ($r['slug'] ?? ''),
            'category' => $category,
            'categoryLabel' => self::CATEGORY_LABELS[$category] ?? 'Therapies',
            'title' => (string) ($r['title'] ?? ''),
            'icon' => (string) ($r['icon'] ?? 'icon-leaf'),
            'image' => (string) ($r['image'] ?? ''),
            'intro' => (string) ($r['intro'] ?? ''),
            'about' => self::decodeList($r['about'] ?? null),
            'methods' => $methods,
            'benefits' => self::decodeList($r['benefits'] ?? null),
            'methodsCount' => count($methods),
        ];
    }

    /**
     * Normalize methods for storage: plain strings stay plain, entries with
     * a definition become ['title' => ..., 'definition' => ...]. Accepts the
     * raw admin payload or an already-encoded JSON string.
     *
     * @return array<int, mixed>
     */
    private static function normalizeMethods(mixed $methods): array
    {
        $items = is_array($methods) ? $methods : (json_decode((string) $methods, true) ?: []);
        $out = [];
        foreach ($items as $m) {
            if (is_string($m) && trim($m) !== '') {
                $out[] = trim($m);
            } elseif (is_array($m)) {
                $title = trim((string) ($m['title'] ?? ''));
                if ($title === '') {
                    continue;
                }
                $def = trim((string) ($m['definition'] ?? ''));
                $out[] = $def !== '' ? ['title' => $title, 'definition' => $def] : $title;
            }
        }

        return $out;
    }

    /**
     * Encode an admin form value to the stored JSON column: arrays are
     * encoded; already-encoded strings pass through untouched.
     */
    private static function asJson(mixed $value): string
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return (string) $value;
    }

    /**
     * Build a URL slug from a title (e.g. "Physical Therapy" → "physical-therapy").
     */
    private static function slugify(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim((string) $slug, '-');

        return $slug === '' ? '' : $slug;
    }
}
