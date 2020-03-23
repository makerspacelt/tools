<?php

namespace App\Form\DataTransformer;

use App\Entity\ToolTag;
use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;

class TagTransformer implements DataTransformerInterface
{
    /** @var TagsRepository */
    private $tagsRepository;

    public function __construct(TagsRepository $tagsRepository)
    {
        $this->tagsRepository = $tagsRepository;
    }

    public function transform($value)
    {
        $tagStr = [];
        foreach ($value->toArray() as $tag) {
            $tagStr[] = $tag->getTag();
        }
        return ['tags' => implode(',', $tagStr)];
    }

    public function reverseTransform($value)
    {
        if (!$value['tags']) {
            return null;
        }

        $tagEntities = new ArrayCollection();
        foreach (explode(',', $value['tags']) as $tag) {
            $tag = trim(strtolower($tag));
            $tagObj = $this->tagsRepository->findOneBy(['tag' => $tag]);
            if (!$tagObj) {
                $tagObj = new ToolTag();
                $tagObj->setTag($tag);
            }
            $tagEntities[] = $tagObj;
        }
        return $tagEntities;
    }

}
