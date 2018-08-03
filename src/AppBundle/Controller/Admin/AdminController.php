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
        if ($request->request->count() == 5) {
            $user = $request->request->get('username');
            $email = $request->request->get('email');
            if (($user != null) && ($email != null)) {
                // patikrinam, ar vartotojas egzistuoja su tokiais username arba email
                $repo = $this->getDoctrine()->getRepository(User::class);
                $obj = $repo->findOneBy(array('username' => $user));
                if ($obj != null) {
                    $this->addFlash('error', 'Username already exists!');
                    return $this->render('admin/add_user.html.twig', $request->request->all());
                }
                $obj = $repo->findOneBy(array('email' => $email));
                if ($obj != null) {
                    $this->addFlash('error', 'Email already exists!');
                    return $this->render('admin/add_user.html.twig', $request->request->all());
                }
                // neegzistuoja, tai sukuriam
                $entityManager = $this->getDoctrine()->getManager();

                $postArr = $request->request->all();
                $user = new User();
                $user->setFullname($postArr['fullname']);
                $user->setUsername(preg_replace('#\\s#', '', $postArr['username']));
                $user->setEmail($postArr['email']);
                $user->setPassword($this->encoder->encodePassword($user, $postArr['password']));
                $user->setRole($postArr['role']);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'User created!');
                return $this->redirectToRoute('admin_users');
            }
        }
        return $this->render('admin/add_user.html.twig');
    }

    /**
     * @Route("/users/editUser", name="admin_edit_user")
     */
    public function editUser(Request $request) {
        if ($request->request->count() == 5) {
            $repo = $this->getDoctrine()->getRepository(User::class);
            $user = $repo->findOneBy(array('username' => $request->request->get('username')));
            if ($request->request->get('fullname')) $user->setFullname($request->request->get('fullname'));
            if ($request->request->get('email')) $user->setEmail($request->request->get('email'));
            $passwd = trim($request->request->get('password'));
            if ($passwd && ($passwd != '')) {
                /** @noinspection PhpParamsInspection */
                $user->setPassword($this->encoder->encodePassword($user, $request->request->get('password')));
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'User modified!');
            return $this->redirectToRoute('admin_users');
        }
        return $this->render('admin/edit_user.html.twig', $request->request->all());
    }

    /**
     * @Route("/users/delUser", name="admin_del_user")
     */
    public function deleteUser(Request $request) {
        $userid = $request->request->get('userid');
        if ($userid != null) {
            $repo = $this->getDoctrine()->getRepository(User::class);
            $user = $repo->findOneBy(array('id' => $userid));
            $repo = $this->getDoctrine()->getManager();
            $repo->remove($user);
            $repo->flush();
            $this->addFlash('success', sprintf('User "%s" removed!', $user->getUsername()));
        }
        return $this->redirectToRoute('admin_users');
    }

}