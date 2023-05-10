<?php

namespace App\Image;

class PathManager
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

    public function getOriginalImagesDir(): string
    {
        return $this->imagesDir;
    }

    public function getPathToOriginalImage($imageFileName): string
    {
        return self::buildPath($this->imagesDir, $imageFileName);
    }

    public function getPathToThumbnail($imageFileName): string
    {
        return self::buildPath($this->imagesThumbnailsDir, $imageFileName);
    }

    public function getPathToPreviewImage($imageFileName): string
    {
        return self::buildPath($this->imagesPreviewDir, $imageFileName);
    }

    private static function buildPath(string $dir, string $fileName): string
    {
        return rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $fileName;
    }
}
