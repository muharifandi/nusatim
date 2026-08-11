<?php

namespace Tests\Unit;

use App\Services\ImageCompressionService;
use PHPUnit\Framework\TestCase;

class ImageCompressionServiceTest extends TestCase
{
    private function makeJpeg(int $width, int $height, int $quality = 95): string
    {
        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 100, 150, 200));
        ob_start();
        imagejpeg($image, null, $quality);
        $contents = ob_get_clean();
        imagedestroy($image);

        return $contents;
    }

    private function makeTransparentPng(int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);
        ob_start();
        imagepng($image, null, 0);
        $contents = ob_get_clean();
        imagedestroy($image);

        return $contents;
    }

    public function test_downscales_an_oversized_jpeg_and_shrinks_it(): void
    {
        $original = $this->makeJpeg(3000, 2000);

        $compressed = (new ImageCompressionService())->compress($original);

        $this->assertNotNull($compressed);
        $this->assertLessThan(strlen($original), strlen($compressed));

        $info = getimagesizefromstring($compressed);
        $this->assertSame(2000, $info[0]);
        $this->assertSame(1333, $info[1]);
    }

    public function test_does_not_upscale_an_image_already_within_the_size_cap(): void
    {
        $original = $this->makeJpeg(400, 300);

        $compressed = (new ImageCompressionService())->compress($original);

        // Either untouched (null) or re-encoded at the SAME dimensions -
        // never upscaled.
        if ($compressed !== null) {
            $info = getimagesizefromstring($compressed);
            $this->assertSame(400, $info[0]);
            $this->assertSame(300, $info[1]);
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_preserves_png_transparency(): void
    {
        $original = $this->makeTransparentPng(1200, 1200);

        $compressed = (new ImageCompressionService())->compress($original);

        $this->assertNotNull($compressed);
        $image = imagecreatefromstring($compressed);
        $this->assertTrue(imagecolorat($image, 0, 0) >> 24 !== 0, 'expected the pixel to still carry alpha transparency, not be flattened to opaque black');
        imagedestroy($image);
    }

    public function test_returns_null_for_non_image_bytes(): void
    {
        $compressed = (new ImageCompressionService())->compress('%PDF-1.4 not actually an image');

        $this->assertNull($compressed);
    }

    public function test_returns_null_for_animated_gif_bytes(): void
    {
        // GD can technically decode a GIF, but we deliberately don't re-encode
        // it - imagegif() would collapse an animation to its first frame.
        $image = imagecreatetruecolor(200, 200);
        imagefill($image, 0, 0, imagecolorallocate($image, 10, 20, 30));
        ob_start();
        imagegif($image);
        $gif = ob_get_clean();
        imagedestroy($image);

        $compressed = (new ImageCompressionService())->compress($gif);

        $this->assertNull($compressed);
    }
}
