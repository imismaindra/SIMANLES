<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizerService
{
    /**
     * Optimalkan dan simpan gambar profil ke storage disk 'public' (resize & kompresi WebP).
     */
    public static function optimizeAndStore(
        UploadedFile $file,
        string $directory = 'profil',
        int $maxDimension = 500,
        int $quality = 82
    ): string {
        $realPath = $file->getRealPath();

        if (! extension_loaded('gd') || ! file_exists($realPath)) {
            return $file->store($directory, 'public');
        }

        $imageInfo = @getimagesize($realPath);
        if (! $imageInfo) {
            return $file->store($directory, 'public');
        }

        $mime = $imageInfo['mime'];
        $srcImage = null;

        switch ($mime) {
            case 'image/jpeg':
                $srcImage = @imagecreatefromjpeg($realPath);
                break;
            case 'image/png':
                $srcImage = @imagecreatefrompng($realPath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $srcImage = @imagecreatefromwebp($realPath);
                }
                break;
        }

        if (! $srcImage) {
            return $file->store($directory, 'public');
        }

        // Koreksi orientasi EXIF untuk JPEG jika ada
        if (function_exists('exif_read_data') && ($mime === 'image/jpeg')) {
            $exif = @exif_read_data($realPath);
            if (! empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $srcImage = imagerotate($srcImage, 180, 0);
                        break;
                    case 6:
                        $srcImage = imagerotate($srcImage, -90, 0);
                        break;
                    case 8:
                        $srcImage = imagerotate($srcImage, 90, 0);
                        break;
                }
            }
        }

        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // Pertahankan aspect ratio jika dimensi melebihi batas maksimum
        if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            $ratio = min($maxDimension / $origWidth, $maxDimension / $origHeight);
            $newWidth = (int) max(1, round($origWidth * $ratio));
            $newHeight = (int) max(1, round($origHeight * $ratio));
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }

        $destImage = imagecreatetruecolor($newWidth, $newHeight);

        // Pertahankan transparansi untuk PNG & WebP
        imagealphablending($destImage, false);
        imagesavealpha($destImage, true);
        $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
        imagefilledrectangle($destImage, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled(
            $destImage,
            $srcImage,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $origWidth,
            $origHeight
        );

        $useWebp = function_exists('imagewebp');
        $extension = $useWebp ? 'webp' : 'jpg';
        $filename = Str::random(40).'.'.$extension;
        $relativeFilename = $directory.'/'.$filename;

        ob_start();
        if ($useWebp) {
            imagewebp($destImage, null, $quality);
        } else {
            imagejpeg($destImage, null, $quality);
        }
        $stream = ob_get_clean();

        imagedestroy($srcImage);
        imagedestroy($destImage);

        if (! empty($stream)) {
            Storage::disk('public')->put($relativeFilename, $stream);

            return $relativeFilename;
        }

        return $file->store($directory, 'public');
    }
}
