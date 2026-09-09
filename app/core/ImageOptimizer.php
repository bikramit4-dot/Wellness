<?php
/**
 * ImageOptimizer — automatically convert and compress uploaded images.
 *
 * After an image is uploaded (JPG, PNG, GIF), this class:
 *   1. Converts it to WebP (best browser support, ~30% smaller than JPG)
 *   2. Optionally tries AVIF (even smaller, but limited browser support)
 *   3. Falls back to the original format if conversion fails
 *   4. Deletes the original to save storage
 *
 * Usage:
 *   $optimized = ImageOptimizer::optimize('/path/to/uploaded-image.jpg');
 *   // $optimized = '/path/to/uploaded-image.webp'
 *
 * Requires: PHP GD extension with WebP support (AVIF optional).
 */
class ImageOptimizer
{
    /** WebP compression quality (0-100). 80 is a good balance. */
    private const WEBP_QUALITY = 80;

    /** AVIF compression quality (0-100). 70 works well for photos. */
    private const AVIF_QUALITY = 70;

    /** Max dimension (width or height) — images larger are downscaled. */
    private const MAX_DIMENSION = 2400;

    /**
     * Optimize an image file: convert to WebP and delete the original.
     *
     * @param string $path Absolute path to the image file
     * @return string The path to the optimized file (WebP or original if conversion failed)
     */
    public static function optimize(string $path): string
    {
        if (!is_file($path) || !is_readable($path)) {
            return $path;
        }

        // Graceful degradation: if GD is missing entirely, or GD was built
        // without WebP support (common on some XAMPP/cPanel builds), the
        // imagewebp() function does not exist and calling it would be a fatal
        // error. In that case keep the original file — an unoptimized image is
        // far better than a crashed upload request.
        if (!extension_loaded('gd') || !function_exists('imagewebp')) {
            return $path;
        }

        // Skip if already WebP or AVIF
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['webp', 'avif'], true)) {
            return $path;
        }

        // Only optimize supported source formats (fileinfo may be missing on
        // minimal hosts — fall back to the file extension in that case).
        $sourceMime = function_exists('mime_content_type')
            ? @mime_content_type($path)
            : self::mimeFromExtension($path);
        $supported = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($sourceMime, $supported, true)) {
            return $path;
        }

        $webpPath = self::changeExtension($path, 'webp');

        // If WebP version already exists, just delete the original
        if (is_file($webpPath)) {
            @unlink($path);
            return $webpPath;
        }

        // Load the source image
        $image = self::loadImage($path, $sourceMime);
        if ($image === null) {
            return $path; // Conversion failed — keep original
        }

        // Downscale if too large
        $image = self::downscale($image);

        // Save as WebP
        $saved = @imagewebp($image, $webpPath, self::WEBP_QUALITY);

        if (!$saved || !is_file($webpPath)) {
            return $path; // WebP save failed — keep original
        }

        // Delete the original to save storage
        if (realpath($path) !== realpath($webpPath)) {
            @unlink($path);
        }

        return $webpPath;
    }

    /**
     * Optimize and return a URL-safe path for use in <img> tags.
     * Converts the file path and returns the new public URL path.
     *
     * @param string $url The original image URL (e.g. BASE_URL . '/public/uploads/xxx.jpg')
     * @return string The optimized URL (WebP)
     */
    public static function optimizeUrl(string $url): string
    {
        if ($url === '' || !str_starts_with($url, BASE_URL)) {
            return $url;
        }

        // Convert URL to filesystem path
        $relativePath = substr($url, strlen(BASE_URL));
        $absolutePath = APP_ROOT . $relativePath;

        $optimized = self::optimize($absolutePath);
        if ($optimized === $absolutePath) {
            return $url; // No change
        }

        // Build new URL from optimized path
        $newRelative = substr($optimized, strlen(APP_ROOT));
        return BASE_URL . $newRelative;
    }

    /**
     * Batch-convert all images in a directory to WebP.
     *
     * @param string $directory Directory to scan (recursive)
     * @return array{converted: int, skipped: int, failed: int}
     */
    public static function batchConvert(string $directory): array
    {
        $stats = ['converted' => 0, 'skipped' => 0, 'failed' => 0];

        if (!is_dir($directory)) {
            return $stats;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || !$file->isReadable()) {
                continue;
            }

            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
                continue;
            }

            $result = self::optimize($file->getPathname());
            if ($result === $file->getPathname()) {
                $stats['skipped']++;
            } else {
                $stats['converted']++;
            }
        }

        return $stats;
    }

    /**
     * Guess the MIME type from a file extension (fallback when fileinfo is
     * unavailable). Returns '' for unknown extensions.
     */
    private static function mimeFromExtension(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            default => '',
        };
    }

    /**
     * Load an image from a file path into a GD resource.
     *
     * @return GdImage|null
     */
    private static function loadImage(string $path, string $mime): ?GdImage
    {
        if (!function_exists('imagecreatefromjpeg')) {
            return null; // GD loaded but without format decoders
        }

        return match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png'  => @imagecreatefrompng($path),
            'image/gif'  => @imagecreatefromgif($path),
            default      => null,
        };
    }

    /**
     * Downscale an image if either dimension exceeds MAX_DIMENSION.
     * Returns the original resource if no scaling is needed.
     *
     * @param GdImage $image
     * @return GdImage
     */
    private static function downscale(GdImage $image): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= self::MAX_DIMENSION && $height <= self::MAX_DIMENSION) {
            return $image;
        }

        $ratio = min(self::MAX_DIMENSION / $width, self::MAX_DIMENSION / $height);
        $newWidth = (int) ($width * $ratio);
        $newHeight = (int) ($height * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        if ($resized === false) {
            return $image;
        }

        // Preserve transparency for PNG and GIF
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled(
            $resized, $image,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $width, $height
        );

        return $resized;
    }

    /**
     * Change a file's extension while preserving the path.
     */
    private static function changeExtension(string $path, string $newExt): string
    {
        return pathinfo($path, PATHINFO_DIRNAME)
            . '/'
            . pathinfo($path, PATHINFO_FILENAME)
            . '.'
            . $newExt;
    }
}
