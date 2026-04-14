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

define('UPLOAD_MAX_BYTES',   100 * 1024 * 1024); // 100 MB — allows raw/DNG drone files
define('PROJECT_IMAGE_MAX_BYTES', 400 * 1024);   // 400 KB — TARGET output size after optimization
define('UPLOAD_ALLOWED_MIME', [
    // Web formats — handled directly by GD
    'image/jpeg', 'image/png', 'image/webp', 'image/gif',
    // Camera/RAW formats — converted to JPEG via Imagick first
    'image/tiff', 'image/x-tiff',
    'image/heic', 'image/heif',
    'image/x-adobe-dng', 'image/x-canon-cr2', 'image/x-canon-cr3',
    'image/x-nikon-nef', 'image/x-sony-arw', 'image/x-fuji-raf',
    'image/x-panasonic-rw2', 'image/x-olympus-orf',
    // Some browsers send these as octet-stream — we sniff the file extension as a fallback
    'application/octet-stream',
]);

// Absolute filesystem root for the public directory.
// Local dev:  /repo/public  (includes/ is a sibling of public/)
// Live server: /home/parths5/moksha.construction/  (includes/ lives INSIDE docroot)
$_publicCandidates = [
    __DIR__ . '/../public',  // local dev layout
    __DIR__ . '/..',         // live server layout (includes/ inside docroot)
];
$_publicResolved = null;
foreach ($_publicCandidates as $_pc) {
    $_real = realpath($_pc);
    if ($_real !== false && is_dir($_real . DIRECTORY_SEPARATOR . 'assets')) {
        $_publicResolved = $_real;
        break;
    }
}
if ($_publicResolved === null) {
    // Fallback: trust the second candidate even if assets/ check fails (fresh install)
    $_publicResolved = realpath(__DIR__ . '/..') ?: __DIR__ . '/..';
}
define('PUBLIC_ROOT', $_publicResolved);
unset($_publicCandidates, $_publicResolved, $_pc, $_real);

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
function processImage(string $tmpPath, string $destDir, string $filename, int $maxWidth = 2000, ?int $maxBytes = null): string
{
    // $maxBytes here is the SOURCE upload cap; defaults to UPLOAD_MAX_BYTES (100MB).
    // Output is always squeezed under PROJECT_IMAGE_MAX_BYTES via iterative quality drop.
    _validateUpload($tmpPath, $maxBytes);

    $destDir  = _ensureDir($destDir);
    $filename = _sanitizeFilename($filename);
    $prefix   = bin2hex(random_bytes(6));
    $outName  = $prefix . '-' . $filename . '.webp';
    $outPath  = $destDir . DIRECTORY_SEPARATOR . $outName;

    // Convert non-web formats (DNG/RAW/HEIC/TIFF) to JPEG first via Imagick
    $workingPath = _ensureGdReadable($tmpPath);
    $workingIsTemp = $workingPath !== $tmpPath;

    try {
        $image = _loadImage($workingPath);
        $image = _resizeIfNeeded($image, $maxWidth);

        // Iterative quality drop until output is under PROJECT_IMAGE_MAX_BYTES
        $quality = 85;
        $attempts = 0;
        do {
            if (!imagewebp($image, $outPath, $quality)) {
                imagedestroy($image);
                throw new RuntimeException("Failed to save WebP image: {$outPath}");
            }
            $size = filesize($outPath);
            if ($size === false || $size <= PROJECT_IMAGE_MAX_BYTES) break;
            $quality -= 12;
            $attempts++;
        } while ($quality >= 30 && $attempts < 6);

        imagedestroy($image);
    } finally {
        if ($workingIsTemp && is_file($workingPath)) {
            @unlink($workingPath);
        }
    }

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
function _validateUpload(string $path, ?int $maxBytes = null): void
{
    if (!is_file($path) || !is_readable($path)) {
        throw new RuntimeException('Uploaded file is missing or not readable.');
    }

    $cap = $maxBytes ?? UPLOAD_MAX_BYTES;

    // Size check
    $size = filesize($path);
    if ($size === false || $size > $cap) {
        $label = $cap >= 1024 * 1024
            ? number_format($cap / 1048576, 1) . ' MB'
            : number_format($cap / 1024, 0) . ' KB';
        $actualMb = $size !== false ? number_format($size / 1048576, 1) . ' MB' : '?';
        throw new RuntimeException("File too large ({$actualMb}). Max allowed: {$label}.");
    }

    // MIME check via fileinfo (more reliable than $_FILES['type'])
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($path) ?: 'application/octet-stream';

    if (!in_array($mime, UPLOAD_ALLOWED_MIME, true)) {
        // Some formats (DNG, HEIC, etc.) get reported as octet-stream — accept and let _ensureGdReadable convert
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $rawExts = ['dng', 'cr2', 'cr3', 'nef', 'arw', 'raf', 'rw2', 'orf', 'heic', 'heif', 'tiff', 'tif'];
        if (!in_array($ext, $rawExts, true)) {
            throw new RuntimeException("Unsupported file type '{$mime}'. Please upload an image (JPEG, PNG, WebP, HEIC, TIFF, or RAW/DNG).");
        }
    }
}

/**
 * If the source file is in a format GD cannot read (DNG, RAW, HEIC, TIFF, etc.),
 * convert it to a temporary JPEG using Imagick (preferred) or the `convert` CLI.
 *
 * @return string  Either the original $path or a new temp .jpg path that the caller must clean up.
 * @throws RuntimeException
 */
function _ensureGdReadable(string $path): string
{
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($path) ?: 'application/octet-stream';
    $ext   = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    $gdNative = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (in_array($mime, $gdNative, true)) {
        return $path; // GD can read it directly
    }

    // Need conversion — try Imagick first, then shell `convert`
    $tmpJpg = tempnam(sys_get_temp_dir(), 'mok_conv_') . '.jpg';

    if (extension_loaded('imagick')) {
        try {
            $im = new Imagick();
            $im->setResolution(150, 150);
            $im->readImage($path);
            $im->setImageFormat('jpeg');
            $im->setImageCompressionQuality(90);
            // Auto-rotate based on EXIF
            if (method_exists($im, 'autoOrient')) {
                $im->autoOrient();
            }
            // Flatten transparency to white
            $im->setBackgroundColor('white');
            $im = $im->flattenImages();
            $im->writeImage($tmpJpg);
            $im->clear();
            $im->destroy();
            return $tmpJpg;
        } catch (Throwable $e) {
            error_log('Imagick conversion failed: ' . $e->getMessage());
            // Fall through to shell convert
        }
    }

    if (function_exists('exec')) {
        $cmd = sprintf('convert %s -auto-orient -background white -flatten -quality 90 %s 2>&1',
            escapeshellarg($path), escapeshellarg($tmpJpg));
        $output = [];
        $rc = 0;
        @exec($cmd, $output, $rc);
        if ($rc === 0 && is_file($tmpJpg) && filesize($tmpJpg) > 0) {
            return $tmpJpg;
        }
        error_log('convert CLI failed: ' . implode("\n", $output));
    }

    // No converter available
    if (is_file($tmpJpg)) @unlink($tmpJpg);
    throw new RuntimeException("Cannot read '{$ext}' files on this server. Please upload a JPEG, PNG, or WebP instead.");
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
