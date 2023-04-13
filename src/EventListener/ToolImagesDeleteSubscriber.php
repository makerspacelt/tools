<?php

namespace App\EventListener;

use App\Entity\ToolPhoto;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class ToolImagesDeleteSubscriber implements EventSubscriber
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

    public function getSubscribedEvents(): array
    {
        return [Events::postRemove];
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // TODO: manage these from single place

        if ($entity instanceof ToolPhoto) {
            unlink($this->imagesDir . DIRECTORY_SEPARATOR . $entity->getFileName());
            unlink($this->imagesThumbnailsDir . DIRECTORY_SEPARATOR . $entity->getFileName());
            unlink($this->imagesPreviewDir . DIRECTORY_SEPARATOR . $entity->getFileName());
        }
    }
}
