<?php

namespace App\Controller;

use App\Repository\ToolsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToolController extends AbstractController
{
    private ToolsRepository $toolsRepo;

    public function __construct(ToolsRepository $toolsRepo)
    {
        $this->toolsRepo = $toolsRepo;
    }

    /**
     * @Route("/tool/{code}", name="tool_page")
     * @param int|null $code
     * @return Response
     */
    public function tool($code = null): Response
    {
        if ($code) {
            $tool = $this->toolsRepo->findOneBy(['code' => $code]);
            if ($tool) {
                return $this->render(
                    'tool.html.twig',
                    [
                        'tool' => $tool,
                    ]
                );
            }
        }

        return $this->redirectToRoute('index_page');
    }
}
