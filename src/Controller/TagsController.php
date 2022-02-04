<?php

namespace App\Controller;

use App\Repository\TagsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TagsController extends AbstractController
{
    private TagsRepository $tagsRepo;

    public function __construct(TagsRepository $tagsRepo)
    {
        $this->tagsRepo = $tagsRepo;
    }

    public function tagsList(): Response
    {
        return $this->render(
            'tags_list.html.twig',
            [
                'tags' => $this->tagsRepo->findAll(),
            ]
        );
    }
}
