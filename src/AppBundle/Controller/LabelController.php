<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LabelController extends Controller {

    /**
     * @Route("/label/{code}", name="tool_label_generator")
     */
    function generateLabel($code = null) {
        if ($code) {

        } else {
            return $this->redirectToRoute('index_page');
        }
    }

}