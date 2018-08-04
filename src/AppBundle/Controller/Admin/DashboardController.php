<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DashboardController extends Controller{

    /**
     * @Route("/", name="admin_homepage")
     */
    public function index() {
        return $this->render('admin/index.html.twig');
    }

}