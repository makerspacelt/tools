<?php

namespace App\EventListener;

use App\Entity\ToolPhoto;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class ToolImagesDeleteSubscriber implements EventSubscriber
{
    /** @var string */
    private $imagesDir;

    public function __construct(string $imagesDir)
    {
        $this->imagesDir = $imagesDir;
    }

    public function getSubscribedEvents()
    {
        return [Events::postRemove];
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof ToolPhoto) {
            return;
        }

        unlink($this->imagesDir . DIRECTORY_SEPARATOR . $entity->getFileName());
    }
}
