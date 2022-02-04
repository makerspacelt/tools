<?php

namespace App\Form\DataTransformer;

use App\Entity\ToolTag;
use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;

class TagTransformer implements DataTransformerInterface
{
    private TagsRepository $tagsRepository;

    public function __construct(TagsRepository $tagsRepository)
    {
        $this->tagsRepository = $tagsRepository;
    }

    public function transform($value): array
    {
        $tagStr = [];
        foreach ($value->toArray() as $tag) {
            $tagStr[] = $tag->getTag();
        }
        return ['tags' => implode(',', $tagStr)];
    }

    public function reverseTransform($value): ?ArrayCollection
    {
        if (!$value['tags']) {
            return null;
        }

        $tagEntities = new ArrayCollection();
        foreach (explode(',', $value['tags']) as $tag) {
            $tag = strtolower(trim($tag));
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
