<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller {

    /**
     * @Route("/", name="index_page")
     */
    public function index() {
        return $this->render('index.html.twig');
    }

}