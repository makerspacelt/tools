<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller {

    /**
     * @Route("/", name="admin_homepage")
     */
    public function index() {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/users", name="admin_users")
     */
    public function users() {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        return $this->render('admin/users.html.twig', array('users' => $users));
    }

    /**
     * @Route("/users/addUser", name="admin_add_user")
     */
    public function addUser(Request $request) {
        if ($request->request->count() == 5) {
            // sukurti pavyko
//            $this->addFlash('success', 'User created!');
//            return $this->redirectToRoute('admin_users');

            // sukurti nepavyko
            
        }
        return $this->render('admin/add_edit_user.html.twig');
    }

}