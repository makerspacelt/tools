<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tool;
use AppBundle\Entity\ToolTag;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ToolController extends Controller {

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
