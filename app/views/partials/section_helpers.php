<?php
/**
 * Helpers for reading editable page-section content in public views.
 *
 * `$sections` is an array keyed [section_key][field] with data-file defaults
 * already merged, so every expected field exists. These helpers add a final
 * default for safety.
 */

if (!function_exists('sec')) {
    function sec(array $sections, string $key, string $field, string $default = ''): string
    {
        $v = (string) ($sections[$key][$field] ?? '');
        return trim($v) !== '' ? $v : $default;
    }
}

/**
 * Parse a section's `extras` (a JSON array of "Label | URL | style" lines)
 * into structured button rows.
 *
 * @return array<int, array{label: string, url: string, style: string}>
 */
if (!function_exists('sec_buttons')) {
    function sec_buttons(array $sections, string $key): array
    {
        $lines = $sections[$key]['extras'] ?? [];
        if (!is_array($lines)) {
            $lines = [];
        }
        $buttons = [];
        $styles = ['primary', 'light', 'dark', 'outline'];
        foreach ($lines as $line) {
            $parts = array_map('trim', explode('|', (string) $line, 3));
            if (($parts[0] ?? '') === '') {
                continue;
            }
            $style = strtolower($parts[2] ?? 'primary');
            if (!in_array($style, $styles, true)) {
                $style = 'primary';
            }
            $buttons[] = [
                'label' => $parts[0],
                'url' => $parts[1] ?? '#',
                'style' => $style,
            ];
        }

        return $buttons;
    }
}

/**
 * Build an href for a page-section link/button URL. Full external URLs
 * (http/https) are kept as-is; anything else is treated as an internal path
 * and prefixed with BASE_URL.
 */
if (!function_exists('sec_url')) {
    function sec_url(string $url): string
    {
        $url = trim($url);
        if ($url !== '' && !preg_match('~^(https?:)?//~i', $url) && $url[0] !== '#') {
            $url = BASE_URL . $url;
        }

        return $url;
    }
}

/**
 * Normalize a social-link URL entered in the admin panel.
 *
 * "facebook.com/page" or "www.tiktok.com/@x" → "https://facebook.com/page"
 * (so it opens the social network instead of a page inside this project).
 * Full URLs (https://…), internal paths (/page → BASE_URL./page) and "#"
 * are kept/normalized as appropriate.
 */
if (!function_exists('sec_social_url')) {
    function sec_social_url(string $url): string
    {
        $url = trim($url);
        if ($url === '' || $url === '#') {
            return '#';
        }
        // Already absolute (http://, https://, protocol-relative //) → keep.
        if (preg_match('~^(https?:)?//~i', $url)) {
            return $url;
        }
        // Internal path starting with "/" → prefix with BASE_URL.
        if ($url[0] === '/') {
            return BASE_URL . $url;
        }
        // Looks like a domain (contains a dot) → force https:// in front.
        if (str_contains($url, '.')) {
            return 'https://' . $url;
        }
        // Anything else: treat as an internal path.
        return BASE_URL . '/' . ltrim($url, '/');
    }
}

/**
 * Parse "Title | Text" lines (e.g. badges, info items, stat values).
 *
 * @return array<int, array{title: string, text: string}>
 */
if (!function_exists('sec_pairs')) {
    function sec_pairs(array $sections, string $key, string $field = 'extras'): array
    {
        $lines = $sections[$key][$field] ?? [];
        if (!is_array($lines)) {
            $lines = [];
        }
        $pairs = [];
        foreach ($lines as $line) {
            $parts = array_map('trim', explode('|', (string) $line, 2));
            $pairs[] = ['title' => $parts[0] ?? '', 'text' => $parts[1] ?? ''];
        }

        return $pairs;
    }
}
