<?php
/**
 * Temporary repair for app/data/*.php files broken by the localize-images
 * migration, which replaced the URL inside the existing single-quoted string
 * and left: 'image' => 'BASE_URL . '/...jpg'',
 * Fix to:        'image' => BASE_URL . '/...jpg',
 */

foreach (['therapies', 'posts', 'gallery', 'features'] as $name) {
    $path = dirname(__DIR__) . '/data/' . $name . '.php';
    if (!is_file($path)) {
        continue;
    }
    $content = (string) file_get_contents($path);
    $fixed = preg_replace(
        "#'BASE_URL \\. '(public/uploads/photos/photo-[0-9a-zA-Z-]+\\.jpg)''#",
        "BASE_URL . '\$1'",
        $content
    );
    if ($fixed !== $content) {
        file_put_contents($path, $fixed);
        echo "$name.php fixed\n";
    }
}

echo "Done.\n";
