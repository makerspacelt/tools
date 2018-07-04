<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController {

    /**
     * @Route("/", name="admin_homepage")
     */
    public function index() {
        return new Response('SUCCESS');
}

}