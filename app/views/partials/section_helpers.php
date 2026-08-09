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
