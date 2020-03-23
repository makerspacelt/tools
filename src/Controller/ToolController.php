<?php

namespace App\Controller;

use App\Repository\TagsRepository;
use App\Repository\ToolsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToolController extends AbstractController
{
    /** @var TagsRepository */
    private $tagsRepo;

    /** @var ToolsRepository */
    private $toolsRepo;

    public function __construct(TagsRepository $tagsRepo, ToolsRepository $toolsRepo)
    {
        $this->tagsRepo = $tagsRepo;
        $this->toolsRepo = $toolsRepo;
    }

    /**
     * @Route("/tool/{code}", name="tool_page")
     * @param int|null $code
     * @return Response
     */
    public function tool($code = null)
    {
        if ($code) {
            $tool = $this->toolsRepo->findOneBy(['code' => $code]);
            if ($tool) {
                return $this->render(
                    'tool.html.twig',
                    [
                        'tags' => $this->tagsRepo->findAll(),
                        'tool' => $tool,
                    ]
                );
            }
        }
        return $this->redirectToRoute('index_page');
    }
}
