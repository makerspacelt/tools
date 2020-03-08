<?php

namespace App\Controller;

use App\Entity\Tool;
use App\Entity\ToolTag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ToolController extends AbstractController
{
    private $tags;

    public function __construct(EntityManagerInterface $em) {
        // get tags here
        $repo = $em->getRepository(ToolTag::class);
        $this->tags = $repo->findAll();
    }

    /**
     * @Route("/tool/{code}", name="tool_page")
     */
    public function tool($code = null) {
        if ($code) {
            $repo = $this->getDoctrine()->getRepository(Tool::class);
            $tool = $repo->findOneBy(array('code' => $code));
            if ($tool) {
                return $this->render('tool.html.twig', array('tags' => $this->tags, 'tool' => $tool));
            }
        }
        return $this->redirectToRoute('index_page');
    }
}
