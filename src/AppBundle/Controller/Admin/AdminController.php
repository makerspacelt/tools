<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller {

    /**
     * @Route("/", name="admin_homepage")
     */
    public function index() {
        // TODO: perkelti į konstruktorių ar kažkur, kad nereiktų kviesti kas kartą
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return new Response('SUCCESS');
    }

    /**
     * @Route("/users", name="admin_users")
     */
    public function users() {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return new Response('users()');
    }

}