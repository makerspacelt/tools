<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminController extends Controller {

    /**
     * @Route("/", name="admin_homepage")
     * @Security("has_role('ROLE_SUPERADMIN')")
     */
    public function index() {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/users", name="admin_users")
     * @Security("has_role('ROLE_SUPERADMIN')")
     */
    public function users() {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        return $this->render('admin/users.html.twig', array('users' => $users));
    }

}