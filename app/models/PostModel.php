<?php
/**
 * Loads blog articles from the `posts` table, falling back to the
 * static data file (app/data/posts.php) when MySQL is unavailable.
 */
class PostModel
{
    /**
     * @return array<string, array<string, mixed>> keyed by slug
     */
    public static function all(): array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $rows = $pdo->query('SELECT * FROM posts ORDER BY date DESC')->fetchAll();
                $out = [];
                foreach ($rows as $r) {
                    $out[$r['slug']] = [
                        'title' => $r['title'],
                        'category' => $r['category'] ?: 'Wellness',
                        'date' => $r['date'] ?? '',
                        'image' => $r['image'] ?? '',
                        'excerpt' => $r['excerpt'] ?? '',
                        'content' => self::decodeList($r['content']),
                        'keyPoints' => self::decodeList($r['key_points']),
                    ];
                }
                if ($out !== []) {
                    return $out;
                }
            } catch (Throwable $e) {
                error_log('[PostModel] Query failed: ' . $e->getMessage());
            }
        }

        return require APP_ROOT . '/app/data/posts.php';
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function find(string $slug): ?array
    {
        $posts = self::all();
        return $posts[$slug] ?? null;
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
