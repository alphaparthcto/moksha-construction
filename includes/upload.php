<?php
/**
 * Moksha Construction — Image Upload & Processing Utilities
 *
 * All image uploads are validated, sanitized, resized if needed,
 * and saved as WebP. Random filename prefixes prevent collisions.
 */

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------

define('UPLOAD_MAX_BYTES',   10 * 1024 * 1024); // 10 MB
define('UPLOAD_ALLOWED_MIME', ['image/jpeg', 'image/png', 'image/webp']);

// Absolute filesystem root for the public directory.
// __DIR__ resolves to /…/includes — so /../public gives us public/.
define('PUBLIC_ROOT', realpath(__DIR__ . '/../public'));

// ---------------------------------------------------------------------------
// Public API
// ---------------------------------------------------------------------------

/**
 * Validate, resize, and save an uploaded image at full resolution.
 *
 * @param  string $tmpPath   Temp path from $_FILES['…']['tmp_name']
 * @param  string $destDir   Absolute filesystem path to destination directory
 * @param  string $filename  Base filename WITHOUT extension (will be suffixed .webp)
 * @param  int    $maxWidth  Resize proportionally if image exceeds this width
 * @return string            Relative web path, e.g. /assets/images/projects/slug/abc123-hero.webp
 *
 * @throws RuntimeException  On any validation or processing failure
 */
function processImage(string $tmpPath, string $destDir, string $filename, int $maxWidth = 2000): string
{
    _validateUpload($tmpPath);

    $destDir  = _ensureDir($destDir);
    $filename = _sanitizeFilename($filename);
    $prefix   = bin2hex(random_bytes(6)); // 12-char hex to prevent overwrites
    $outName  = $prefix . '-' . $filename . '.webp';
    $outPath  = $destDir . DIRECTORY_SEPARATOR . $outName;

    $image = _loadImage($tmpPath);
    $image = _resizeIfNeeded($image, $maxWidth);

    if (!imagewebp($image, $outPath, 85)) {
        imagedestroy($image);
        throw new RuntimeException("Failed to save WebP image: {$outPath}");
    }
    imagedestroy($image);

    return _toWebPath($outPath);
}

/**
 * Validate, resize, and save an uploaded image as a smaller thumbnail.
 *
 * @param  string $sourcePath  Absolute path to the already-saved full-size image,
 *                             OR a tmp upload path to process independently.
 * @param  string $destDir     Absolute filesystem path to destination directory
 * @param  string $filename    Base filename WITHOUT extension
 * @param  int    $thumbWidth  Target width in pixels (height scales proportionally)
 * @return string              Relative web path
 *
 * @throws RuntimeException
 */
function generateThumbnail(string $sourcePath, string $destDir, string $filename, int $thumbWidth = 600): string
{
    // If this looks like an upload tmp path, validate it; otherwise trust it.
    if (str_starts_with($sourcePath, sys_get_temp_dir()) || str_starts_with($sourcePath, '/tmp')) {
        _validateUpload($sourcePath);
    }

    $destDir  = _ensureDir($destDir);
    $filename = _sanitizeFilename($filename);
    $prefix   = bin2hex(random_bytes(6));
    $outName  = $prefix . '-' . $filename . '-thumb.webp';
    $outPath  = $destDir . DIRECTORY_SEPARATOR . $outName;

    $image = _loadImage($sourcePath);
    $image = _resizeIfNeeded($image, $thumbWidth);

    if (!imagewebp($image, $outPath, 85)) {
        imagedestroy($image);
        throw new RuntimeException("Failed to save WebP thumbnail: {$outPath}");
    }
    imagedestroy($image);

    return _toWebPath($outPath);
}

/**
 * Convert an arbitrary string into a URL-safe slug.
 *
 * @param  string $title  Raw title (e.g. "Downtown Office Build — Phase 2!")
 * @return string         Slug  (e.g. "downtown-office-build-phase-2")
 */
function generateSlug(string $title): string
{
    $slug = mb_strtolower(trim($title), 'UTF-8');

    // Transliterate common accented characters
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug) ?: $slug;

    // Replace any whitespace or common separators with a hyphen
    $slug = preg_replace('/[\s\-_]+/', '-', $slug);

    // Strip everything that is not a lowercase letter, digit, or hyphen
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);

    // Collapse multiple hyphens and trim from edges
    $slug = preg_replace('/-{2,}/', '-', $slug);
    return trim($slug, '-');
}

/**
 * Recursively delete an entire project image directory from the filesystem.
 *
 * Only paths that live inside PUBLIC_ROOT/assets/images/projects/ are
 * accepted to prevent accidental deletion outside the expected tree.
 *
 * @param  string $projectDir  Absolute path to the project's image directory
 * @return void
 */
function deleteProjectImages(string $projectDir): void
{
    $allowedBase = PUBLIC_ROOT . DIRECTORY_SEPARATOR
                 . 'assets' . DIRECTORY_SEPARATOR
                 . 'images' . DIRECTORY_SEPARATOR
                 . 'projects' . DIRECTORY_SEPARATOR;

    // Resolve any symlinks / ".." before the safety check
    $real = realpath($projectDir);
    if ($real === false) {
        // Directory does not exist — nothing to do
        return;
    }

    if (!str_starts_with($real . DIRECTORY_SEPARATOR, $allowedBase)) {
        error_log("deleteProjectImages: Refusing to delete outside projects dir: {$real}");
        return;
    }

    _rmdirRecursive($real);
}

// ---------------------------------------------------------------------------
// Private helpers
// ---------------------------------------------------------------------------

/**
 * Validate MIME type and file size of a candidate upload.
 *
 * @throws RuntimeException
 */
function _validateUpload(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        throw new RuntimeException('Uploaded file is missing or not readable.');
    }

    // Size check
    $size = filesize($path);
    if ($size === false || $size > UPLOAD_MAX_BYTES) {
        $mb = number_format(UPLOAD_MAX_BYTES / 1048576, 0);
        throw new RuntimeException("File exceeds the maximum allowed size of {$mb} MB.");
    }

    // MIME check using fileinfo — more reliable than $_FILES['type']
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($path);

    if (!in_array($mime, UPLOAD_ALLOWED_MIME, true)) {
        $allowed = implode(', ', UPLOAD_ALLOWED_MIME);
        throw new RuntimeException("Invalid file type '{$mime}'. Allowed: {$allowed}.");
    }
}

/**
 * Create $dir (and any parents) if it does not exist; return its real path.
 *
 * @throws RuntimeException
 */
function _ensureDir(string $dir): string
{
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            throw new RuntimeException("Could not create directory: {$dir}");
        }
    }

    $real = realpath($dir);
    if ($real === false) {
        throw new RuntimeException("Directory path could not be resolved: {$dir}");
    }
    return $real;
}

/**
 * Sanitize a base filename: strip path traversal sequences and special chars.
 * Returns lowercase alphanumeric-with-hyphens string, max 80 chars.
 */
function _sanitizeFilename(string $filename): string
{
    // Remove any directory components
    $filename = basename($filename);

    // Strip extension if present
    $filename = pathinfo($filename, PATHINFO_FILENAME);

    // Lowercase and slug-ify
    $filename = generateSlug($filename);

    // Hard cap to avoid filesystem limits
    return substr($filename ?: 'image', 0, 80);
}

/**
 * Load a GD image resource from a file, detecting format via MIME type.
 *
 * @throws RuntimeException  If the format is unsupported or loading fails
 * @return \GdImage
 */
function _loadImage(string $path): \GdImage
{
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($path);

    $image = match ($mime) {
        'image/jpeg' => imagecreatefromjpeg($path),
        'image/png'  => imagecreatefrompng($path),
        'image/webp' => imagecreatefromwebp($path),
        default      => throw new RuntimeException("Unsupported image format: {$mime}"),
    };

    if ($image === false) {
        throw new RuntimeException("GD failed to load image: {$path}");
    }

    // Preserve transparency for PNG / WebP sources
    imagealphablending($image, true);
    imagesavealpha($image, true);

    // Auto-rotate JPEG images according to EXIF orientation
    if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
        $exif = @exif_read_data($path);
        $orientation = $exif['Orientation'] ?? 1;
        $image = _applyExifRotation($image, (int)$orientation);
    }

    return $image;
}

/**
 * Resize $image proportionally so its width does not exceed $maxWidth.
 * If already within bounds, returns the original resource unchanged.
 *
 * @param  \GdImage $image
 * @param  int      $maxWidth
 * @return \GdImage
 */
function _resizeIfNeeded(\GdImage $image, int $maxWidth): \GdImage
{
    $srcW = imagesx($image);
    $srcH = imagesy($image);

    if ($srcW <= $maxWidth) {
        return $image; // Nothing to do
    }

    $ratio  = $maxWidth / $srcW;
    $newW   = $maxWidth;
    $newH   = (int)round($srcH * $ratio);

    $canvas = imagecreatetruecolor($newW, $newH);
    if ($canvas === false) {
        throw new RuntimeException('GD failed to create resize canvas.');
    }

    // Preserve alpha channel in the resized canvas
    imagealphablending($canvas, false);
    imagesavealpha($canvas, true);
    $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
    imagefilledrectangle($canvas, 0, 0, $newW, $newH, $transparent);
    imagealphablending($canvas, true);

    if (!imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH)) {
        imagedestroy($canvas);
        throw new RuntimeException('GD resampling failed.');
    }

    imagedestroy($image);
    return $canvas;
}

/**
 * Rotate a GD image according to the EXIF orientation value.
 *
 * @param  \GdImage $image
 * @param  int      $orientation  EXIF orientation tag (1–8)
 * @return \GdImage
 */
function _applyExifRotation(\GdImage $image, int $orientation): \GdImage
{
    return match ($orientation) {
        3 => imagerotate($image, 180, 0) ?: $image,
        6 => imagerotate($image, -90,  0) ?: $image,
        8 => imagerotate($image, 90,  0) ?: $image,
        default => $image,
    };
}

/**
 * Convert an absolute filesystem path to a relative web path.
 *
 * Example: /var/www/html/public/assets/images/projects/x/y.webp
 *       -> /assets/images/projects/x/y.webp
 */
function _toWebPath(string $absPath): string
{
    // Normalise separators on all platforms
    $absPath    = str_replace(DIRECTORY_SEPARATOR, '/', $absPath);
    $publicRoot = str_replace(DIRECTORY_SEPARATOR, '/', PUBLIC_ROOT);

    if (str_starts_with($absPath, $publicRoot)) {
        return substr($absPath, strlen($publicRoot));
    }

    // Fallback: return as-is if outside PUBLIC_ROOT (shouldn't happen)
    return '/' . ltrim($absPath, '/');
}

/**
 * Recursively remove a directory and all its contents.
 */
function _rmdirRecursive(string $dir): void
{
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($items as $item) {
        $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
    }

    rmdir($dir);
}
