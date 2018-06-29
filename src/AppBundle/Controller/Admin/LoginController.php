<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoginController extends Controller {

    /**
     * @Route("/", name="login_page")
     */
    public function index() {
        return $this->render('admin/login.html.twig');
    }

}