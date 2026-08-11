<?php

namespace App\Services;

/**
 * Re-encodes uploaded raster images at a smaller size/quality using GD
 * (bundled with PHP - no Composer package needed, and this environment has
 * no network access to install one anyway). Wired into every Filament
 * FileUpload field via AppServiceProvider so admin-uploaded images (which
 * routinely came in at 1.5MB+ straight off someone's phone) don't ship to
 * every visitor at full size.
 */
class ImageCompressionService
{
    /**
     * Longest-side cap in pixels. Uploads bigger than this get downscaled;
     * anything already smaller is left at its own size (never upscaled -
     * that would only add file weight for zero visual gain).
     */
    private const MAX_DIMENSION = 2000;

    private const JPEG_QUALITY = 82;

    // GD's PNG compression is 0 (none) - 9 (max); 6 balances size vs CPU time.
    private const PNG_COMPRESSION = 6;

    private const WEBP_QUALITY = 82;

    /**
     * Re-encode raw image bytes. Returns the compressed bytes, or null to
     * signal "leave the original alone" - either because this isn't a
     * format we touch (GIF/SVG - re-encoding would break animation or
     * isn't applicable to a vector format), GD couldn't decode it, or the
     * "compressed" result would actually be larger than the input.
     */
    public function compress(string $contents): ?string
    {
        $info = @getimagesizefromstring($contents);
        if ($info === false) {
            return null;
        }

        [$width, $height] = $info;
        $mime = $info['mime'];

        $source = match ($mime) {
            'image/jpeg', 'image/png' => @imagecreatefromstring($contents),
            'image/webp' => function_exists('imagecreatefromstring') ? @imagecreatefromstring($contents) : false,
            default => false, // image/gif (animation), image/svg+xml (vector), anything else: untouched
        };

        if (! $source) {
            return null;
        }

        // Preserve transparency instead of flattening it to black - a lot
        // of what gets uploaded here is logos/icons with alpha. Needed on
        // $source unconditionally (not just when resizing below) since
        // imagepng()/imagewebp() only keep alpha if savealpha is set on the
        // exact resource being encoded.
        imagealphablending($source, false);
        imagesavealpha($source, true);

        $scale = min(1, self::MAX_DIMENSION / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        if ($scale < 1) {
            $resized = imagecreatetruecolor($targetWidth, $targetHeight);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
            imagedestroy($source);
            $source = $resized;
        }

        ob_start();
        $encoded = match ($mime) {
            'image/jpeg' => imagejpeg($source, null, self::JPEG_QUALITY),
            'image/png' => imagepng($source, null, self::PNG_COMPRESSION),
            'image/webp' => function_exists('imagewebp') ? imagewebp($source, null, self::WEBP_QUALITY) : false,
            default => false,
        };
        $output = ob_get_clean();
        imagedestroy($source);

        if (! $encoded || $output === false || $output === '') {
            return null;
        }

        // Never ship a "compressed" file that's actually bigger (can happen
        // on tiny already-optimized images) - keep the original in that case.
        return strlen($output) < strlen($contents) ? $output : null;
    }
}
