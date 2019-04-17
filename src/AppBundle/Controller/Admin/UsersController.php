<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/users")
 */
class UsersController extends Controller {

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    /**
     * @Route("/", name="admin_users")
     */
    public function users() {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        return $this->render('admin/users/users.html.twig', array('users' => $users));
    }

    private function generateForm(User $user) {
        return $this->createFormBuilder($user)->
        add('fullname', TextType::class, ['required' => true, 'label' => 'Full name', 'attr' => ['class' => 'mb-3']])->
        add('username', TextType::class, ['required' => true, 'disabled' => ($user->getUsername() != ''), 'attr' => ['class' => 'mb-3']])->
        add('password', PasswordType::class, ['required' => ($user->getPassword() == ''), 'attr' => ['class' => 'mb-3']])->
        add('email', EmailType::class, ['required' => true, 'attr' => ['class' => 'mb-3']])->
        add('roles', ChoiceType::class, ['multiple' => true, 'choices' => ['Super admin' => 'ROLE_SUPERADMIN'], 'attr' => ['class' => 'mb-3']])->
        add('save', SubmitType::class, ['label' => 'Submit'])->
        getForm();
    }

    /**
     * @Route("/addUser", name="admin_add_user")
     */
    public function addUser(Request $request) {
        $user = new User();
        $form = $this->generateForm($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formUser = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($formUser);
            $em->flush();
            $this->addFlash('success', 'User created!');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/add_user.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/editUser/{id}", name="admin_edit_user")
     */
    public function editUser(Request $request, User $user) {
        $form = $this->generateForm($user);
        // reikia išsaugoti seną slaptažodį čia, nes po handleRequest() objektas pasikeičia
        $oldPasswd = $user->getPassword();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formUser = $form->getData();
            if ($formUser->getPassword() != null) {
                $formUser->setPassword($this->encoder->encodePassword($user, $formUser->getPassword()));
            } else { // jei naujas neįvestas, tai nustatom seną...
                $formUser->setPassword($oldPasswd);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($formUser);
            $em->flush();
            $this->addFlash('success', 'User modified!');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/edit_user.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delUser", name="admin_del_user")
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