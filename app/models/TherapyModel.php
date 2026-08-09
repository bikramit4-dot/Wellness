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
}
