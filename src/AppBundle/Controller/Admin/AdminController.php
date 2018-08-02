<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends Controller {

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

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
        if ($request->request->count() == 6) {
            $user = $request->request->get('username');
            $email = $request->request->get('email');
            if (($user != null) && ($email != null)) {
                // patikrinam, ar vartotojas egzistuoja su tokiais username arba email
                $repo = $this->getDoctrine()->getRepository(User::class);
                $obj = $repo->findOneBy(array('username' => $user));
                if ($obj != null) {
                    $this->addFlash('error', 'Username already exists!');
                    return $this->render('admin/add_edit_user.html.twig', $request->request->all());
                }
                $obj = $repo->findOneBy(array('email' => $email));
                if ($obj != null) {
                    $this->addFlash('error', 'Email already exists!');
                    return $this->render('admin/add_edit_user.html.twig', $request->request->all());
                }
                // neegzistuoja, tai sukuriam
                $entityManager = $this->getDoctrine()->getManager();

                $postArr = $request->request->all();
                $user = new User();
                $user->setFullname($postArr['firstname'].' '.$postArr['lastname']);
                $user->setUsername($postArr['username']);
                $user->setEmail($postArr['email']);
                $user->setPassword($this->encoder->encodePassword($user, $postArr['password']));
                $user->setRole($postArr['role']);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'User created!');
                return $this->redirectToRoute('admin_users');
            }
        }
        return $this->render('admin/add_edit_user.html.twig');
    }

}