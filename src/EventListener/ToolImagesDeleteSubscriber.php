<?php

namespace App\EventListener;

use App\Entity\ToolPhoto;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use App\Image\PathManager;

class ToolImagesDeleteSubscriber implements EventSubscriber
{
    private PathManager $pathManager;

    public function __construct(PathManager $pathManager)
    {
        $this->pathManager = $pathManager;
    }

    public function getSubscribedEvents(): array
    {
        return [Events::postRemove];
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof ToolPhoto) {
            unlink($this->pathManager->getPathToOriginalImage($entity->getFileName()));
            unlink($this->pathManager->getPathToThumbnail($entity->getFileName()));
            unlink($this->pathManager->getPathToPreviewImage($entity->getFileName()));
        }
    }
}
