<?php
/**
 * Tiny .env loader — no Composer packages required.
 *
 * Reads KEY=VALUE pairs from a .env file and loads them into the process
 * environment via putenv() so getenv() picks them up. Skips comments,
 * blank lines, and lines without an = sign.
 */
class Dotenv
{
    /**
     * Load environment variables from the given file.
     *
     * Existing env vars are NOT overwritten — so anything already set
     * (e.g. via hosting control panel) takes priority.
     */
    public static function load(string $path): void
    {
        if (!is_file($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments and blank lines
            if ($line === '' || $line[0] === '#') {
                continue;
            }

            // Split on first =
            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }

            $key   = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));

            // Strip surrounding quotes (single or double)
            if (strlen($value) >= 2
                && (($value[0] === '"' && substr($value, -1) === '"')
                    || ($value[0] === "'" && substr($value, -1) === "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            // Don't overwrite existing env vars (hosting panel > .env)
            if (getenv($key) !== false) {
                continue;
            }

            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}
