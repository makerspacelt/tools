<?php

namespace App\EventListener;

use App\Entity\ToolPhoto;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class ToolImagesDeleteSubscriber implements EventSubscriber
{
    private string $imagesDir;

    public function __construct(string $imagesDir)
    {
        $this->imagesDir = $imagesDir;
    }

    public function getSubscribedEvents(): array
    {
        return [Events::postRemove];
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof ToolPhoto) {
            unlink($this->imagesDir . DIRECTORY_SEPARATOR . $entity->getFileName());
        }
    }
}
