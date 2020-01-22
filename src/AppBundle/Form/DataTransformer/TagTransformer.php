<?php


namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\ToolTag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\DataTransformerInterface;

class TagTransformer implements DataTransformerInterface {

    private $manager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
    }

    public function transform($value) {
        $tagStr = array();
        foreach ($value->toArray() as $tag) {
            $tagStr[] = $tag->getTag();
        }
        return array('tags' => implode(',', $tagStr));
    }

    public function reverseTransform($value) {
        if (!$value['tags']) {
            return null;
        }

        $tagsRepo = $this->manager->getRepository(ToolTag::class);
        $tagEntities = new ArrayCollection();
        foreach (explode(',', $value['tags']) as $tag) {
            $tag = trim(strtolower($tag));
            $tagObj = $tagsRepo->findOneBy(array('tag' => $tag));
            if (!$tagObj) {
                $tagObj = new ToolTag();
                $tagObj->setTag($tag);
            }
            $tagEntities[] = $tagObj;
        }
        return $tagEntities;
    }

}
