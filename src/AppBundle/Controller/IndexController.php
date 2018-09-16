<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ToolTag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller {

    /**
     * @Route("/", name="index_page")
     */
    public function index() {
        // get tags
        $repo = $this->getDoctrine()->getRepository(ToolTag::class);
        $tags = $repo->findAll();
        return $this->render('index.html.twig', array('tags' => $tags));
    }

}