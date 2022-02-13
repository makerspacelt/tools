<?php

namespace App\Controller;

use App\Entity\ToolLog;
use App\Form\Type\LogType;
use App\Repository\ToolsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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
    public function tool(Request $request, $code = null): Response
    {
        if ($code) {
            $tool = $this->toolsRepo->findOneBy(['code' => $code]);
            if ($tool) {
                $form = $this->createToolLogForm()->handleRequest($request);
                if($form->isSubmitted() && $form->isValid())
                {
                    $this->toolsRepo->addToolLog($tool, $form->getData());
                }
                return $this->render(
                    'tool.html.twig',
                    [
                        'tool' => $tool,
                        'logform' => $this->createToolLogForm()->createView()
                    ]
                );
            }
        }

        return $this->redirectToRoute('index_page');
    }

    private function createToolLogForm(){
        return $this->createForm(LogType::class, new ToolLog())->add('submit', SubmitType::class);
    }
}
