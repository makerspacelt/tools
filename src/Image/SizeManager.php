<?php

namespace App\Image;

use Exception;
use GdImage;

class SizeManager
{
    private PathManager $pathManager;

    public function __construct(PathManager $pathManager)
    {
        $this->pathManager = $pathManager;
    }

    public function ensureSizes(string $imageFileName): void
    {
        $originalImagePath = $this->pathManager->getPathToOriginalImage($imageFileName);
        if (!file_exists($originalImagePath)) {
            throw new \RuntimeException(sprintf('original image file not found in "%s"', $originalImagePath));
        }

        static::resize($originalImagePath, $this->pathManager->getPathToThumbnail($imageFileName), 250, 250);
        static::resize($originalImagePath, $this->pathManager->getPathToPreviewImage($imageFileName), 1500, 1500);
    }

    private static function resize($sourceFile, $dstFile, $width, $height): void
    {
        list($iwidth, $iheight) = getimagesize($sourceFile);
        $ratio = $iwidth / $iheight;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        [$source, $type] = self::openImage($sourceFile);

        $dst = imagecreatetruecolor($width, $height);
        imagecopyresampled($dst, $source, 0, 0, 0, 0, $width, $height, $iwidth, $iheight);

        self::saveImage($type, $dst, $dstFile);
    }

    /**
     * @return mixed[] array{GdImage|resource, int}
     */
    private static function openImage(string $path): array
    {
        $exifImageType = exif_imagetype($path);

        switch ($exifImageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($path);
                break;
            case IMAGETYPE_BMP:
                $image = imagecreatefrombmp($path);
                break;
            default:
                throw new Exception(sprintf("unsupported image type %d", $exifImageType));
        }

        return [$image, $exifImageType];
    }

    /**
     * @param int $exifImageType
     * @param resource|GdImage $image
     * @param string $path
     */
    private static function saveImage(int $exifImageType, $image, string $path): void
    {
        switch ($exifImageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($image, $path);
                break;
            case IMAGETYPE_BMP:
                imagebmp($image, $path);
                break;
            case IMAGETYPE_PNG:
                imagepng($image, $path);
                break;
            default:
                throw new Exception(sprintf("unsupported image type %d", $exifImageType));
        }
    }
}
