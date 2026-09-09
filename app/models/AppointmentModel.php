<?php
/**
 * Appointment bookings stored in MySQL (`appointments` table).
 * If the database is unavailable, operations fall back to the
 * JSON-lines file (storage/appointments.txt) so bookings never fail.
 */
class AppointmentModel
{
    private const STATUSES = ['new', 'confirmed', 'rejected', 'completed'];

    /* ------------------------------------------------------------------ */
    /* Public API (same for DB and file backends)                         */
    /* ------------------------------------------------------------------ */

    public function save(string $name, string $email, string $phone, string $treatment, string $date, string $time, string $message): void
    {
        error_log('[AppointmentModel] save() called: name=' . $name . ', email=' . $email);
        $data = [
            'id' => bin2hex(random_bytes(8)),
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'treatment' => $treatment,
            'date' => $date,
            'time' => $time,
            'message' => $message,
            'status' => 'new',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO appointments (id, name, email, phone, treatment, date, time, message, status, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $data['id'], $data['name'], $data['email'], $data['phone'],
                    $data['treatment'], $data['date'], $data['time'],
                    $data['message'], $data['status'], $data['created_at'],
                ]);
                return;
            } catch (Throwable $e) {
                error_log('[AppointmentModel] DB save failed, using file fallback: ' . $e->getMessage());
            }
        }

        $this->saveToFile($data);
    }

    /**
     * @return array<int, array<string, string>> newest first
     */
    public function all(): array
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $rows = $pdo->query('SELECT * FROM appointments ORDER BY created_at DESC, id DESC')->fetchAll();
                $out = [];
                foreach ($rows as $r) {
                    $r['status'] = $r['status'] ?? 'new';
                    $out[] = $r;
                }

                return $out;
            } catch (Throwable $e) {
                error_log('[AppointmentModel] DB read failed, using file fallback: ' . $e->getMessage());
            }
        }

        return $this->allFromFile();
    }

    public function delete(string $id): bool
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('DELETE FROM appointments WHERE id = ?');
                $stmt->execute([$id]);

                return $stmt->rowCount() > 0;
            } catch (Throwable $e) {
                error_log('[AppointmentModel] DB delete failed, using file fallback: ' . $e->getMessage());
            }
        }

        return $this->deleteFromFile($id);
    }

    /**
     * Count appointments with the given status.
     */
    public function countByStatus(string $status): int
    {
        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE status = ?');
                $stmt->execute([$status]);
                return (int) $stmt->fetchColumn();
            } catch (Throwable $e) {
                error_log('[AppointmentModel] countByStatus failed: ' . $e->getMessage());
            }
        }

        // File fallback
        $count = 0;
        foreach ($this->allFromFile() as $row) {
            if (($row['status'] ?? 'new') === $status) {
                $count++;
            }
        }
        return $count;
    }

    public function updateStatus(string $id, string $status): bool
    {
        if (!in_array($status, self::STATUSES, true)) {
            return false;
        }

        $pdo = Database::pdo();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('UPDATE appointments SET status = ? WHERE id = ?');
                $stmt->execute([$status, $id]);

                return $stmt->rowCount() > 0;
            } catch (Throwable $e) {
                error_log('[AppointmentModel] DB update failed, using file fallback: ' . $e->getMessage());
            }
        }

        return $this->updateStatusFromFile($id, $status);
    }

    /* ------------------------------------------------------------------ */
    /* File fallback (storage/appointments.txt)                           */
    /* ------------------------------------------------------------------ */

    private function filePath(): string
    {
        return APP_ROOT . '/storage/appointments.txt';
    }

    /**
     * @param array<string, string> $data
     */
    private function saveToFile(array $data): void
    {
        $dir = dirname($this->filePath());
        if (!is_dir($dir)) {
            // 0777 so the web server user (e.g. XAMPP's 'daemon') can write to it.
            mkdir($dir, 0777, true);
        }

        $file = $this->filePath();
        if (!is_file($file)) {
            @touch($file);
        }
        @chmod($file, 0666);

        $line = json_encode($data, JSON_UNESCAPED_SLASHES) . PHP_EOL;
        $bytes = file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
        if ($bytes === false) {
            error_log('[AppointmentModel] Failed to write to ' . $file);
        } else {
            error_log('[AppointmentModel] Saved appointment to file: ' . $data['id'] . ' (' . $bytes . ' bytes)');
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function allFromFile(): array
    {
        $parsed = [];
        foreach ($this->readLines() as $line) {
            $row = json_decode($line, true);
            if (is_array($row)) {
                $row['status'] = $row['status'] ?? 'new';
                $parsed[] = $row;
            }
        }

        usort($parsed, static fn (array $a, array $b): int =>
            strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));

        return $parsed;
    }

    private function deleteFromFile(string $id): bool
    {
        $rows = $this->readLines();
        $found = false;

        foreach ($rows as $i => $line) {
            $row = json_decode($line, true);
            if (is_array($row) && ($row['id'] ?? null) === $id) {
                unset($rows[$i]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            return false;
        }

        $this->writeLines($rows);
        return true;
    }

    private function updateStatusFromFile(string $id, string $status): bool
    {
        $rows = $this->readLines();

        foreach ($rows as $i => $line) {
            $row = json_decode($line, true);
            if (is_array($row) && ($row['id'] ?? null) === $id) {
                $row['status'] = $status;
                $rows[$i] = json_encode($row, JSON_UNESCAPED_SLASHES);
                $this->writeLines($rows);
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    private function readLines(): array
    {
        if (!is_file($this->filePath())) {
            return [];
        }

        return file($this->filePath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    }

    /**
     * @param array<int, string> $lines
     */
    private function writeLines(array $lines): void
    {
        $clean = array_values(array_filter($lines, static fn (string $l): bool => trim($l) !== ''));
        $file = $this->filePath();
        @chmod($file, 0666);
        file_put_contents($file, implode(PHP_EOL, $clean) . PHP_EOL, LOCK_EX);
    }
}
