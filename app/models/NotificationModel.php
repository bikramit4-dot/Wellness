<?php
/**
 * In-app notifications stored in storage/notifications.json.
 *
 * Used to show a notification bell in the admin header when new
 * appointments or QR payments arrive.
 */
class NotificationModel
{
    private const FILE = '/storage/notifications.json';
    private const MAX  = 50; // keep only the newest 50

    /**
     * Add a new notification.
     *
     * @param string $type    'appointment' | 'qr_payment' | 'system'
     * @param string $title   Short headline
     * @param string $message Detail text
     * @param string $link    URL to open when clicked
     */
    public static function add(string $type, string $title, string $message, string $link = ''): void
    {
        $notifications = self::read();
        array_unshift($notifications, [
            'id'         => bin2hex(random_bytes(8)),
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'link'       => $link,
            'read'       => false,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Keep only the newest N
        if (count($notifications) > self::MAX) {
            $notifications = array_slice($notifications, 0, self::MAX);
        }

        self::write($notifications);
    }

    /**
     * Get all notifications (newest first).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return self::read();
    }

    /**
     * Count unread notifications.
     */
    public static function unreadCount(): int
    {
        $count = 0;
        foreach (self::read() as $n) {
            if (empty($n['read'])) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Mark a single notification as read.
     */
    public static function markRead(string $id): void
    {
        $notifications = self::read();
        foreach ($notifications as &$n) {
            if (($n['id'] ?? '') === $id) {
                $n['read'] = true;
                break;
            }
        }
        unset($n);
        self::write($notifications);
    }

    /**
     * Mark all notifications as read.
     */
    public static function markAllRead(): void
    {
        $notifications = self::read();
        foreach ($notifications as &$n) {
            $n['read'] = true;
        }
        unset($n);
        self::write($notifications);
    }

    /**
     * Delete a notification.
     */
    public static function delete(string $id): void
    {
        $notifications = self::read();
        $notifications = array_values(array_filter(
            $notifications,
            static fn(array $n): bool => ($n['id'] ?? '') !== $id
        ));
        self::write($notifications);
    }

    /* ---------- File I/O ---------- */

    private static function filePath(): string
    {
        return APP_ROOT . self::FILE;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function read(): array
    {
        $file = self::filePath();
        if (!is_file($file)) {
            return [];
        }

        $data = json_decode((string) @file_get_contents($file), true);
        return is_array($data) ? $data : [];
    }

    /**
     * @param array<int, array<string, mixed>> $notifications
     */
    private static function write(array $notifications): void
    {
        $file = self::filePath();
        @file_put_contents($file, json_encode($notifications, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    }
}
