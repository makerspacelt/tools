<?php

namespace App\Image;

class SizeManager
{
    private string $imagesDir;
    private string $imagesThumbnailsDir;
    private string $imagesPreviewDir;

    public function __construct(string $imagesDir, string $imagesThumbnailsDir, string $imagesPreviewDir)
    {
        $this->imagesDir = $imagesDir;
        $this->imagesThumbnailsDir = $imagesThumbnailsDir;
        $this->imagesPreviewDir = $imagesPreviewDir;
    }

    public function ensureSizes(string $imageFileName): void
    {
        $originalImagePath = static::buildPath($this->imagesDir, $imageFileName);
        if (!file_exists($originalImagePath)) {
            throw new \RuntimeException(sprintf('original image file not found in "%s"', $originalImagePath));
        }

        static::resize($originalImagePath, static::buildPath($this->imagesThumbnailsDir, $imageFileName), 250, 250);
        static::resize($originalImagePath, static::buildPath($this->imagesPreviewDir, $imageFileName), 1500, 1500);
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

        // TODO: make it work with other formats, not opnly jpeg

        $source = imagecreatefromjpeg($sourceFile);
        $dst = imagecreatetruecolor($width, $height);
        imagecopyresampled($dst, $source, 0, 0, 0, 0, $width, $height, $iwidth, $iheight);
        imagejpeg($dst, $dstFile);
    }

    private static function buildPath(string $dir, string $fileName): string
    {
        return rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $fileName;
    }
}
