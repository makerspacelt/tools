<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/users")
 */
class UsersController extends AbstractController
{
    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/", name="admin_users")
     */
    public function users(): Response
    {
        return $this->render(
            'admin/users/users.html.twig',
            [
                'users' => $this->userRepository->findAll(),
            ]
        );
    }

    private function generateForm(User $user): FormInterface
    {
        return $this->createFormBuilder($user)
            ->add('fullname', TextType::class,
                ['required' => true, 'label' => 'Full name', 'attr' => ['class' => 'mb-3']])
            ->add('username', TextType::class,
                ['required' => true, 'disabled' => ($user->getUsername() != ''), 'attr' => ['class' => 'mb-3']])
            ->add('password', PasswordType::class,
                ['required' => ($user->getPassword() == ''), 'attr' => ['class' => 'mb-3']])
            ->add('email', EmailType::class, ['required' => true, 'attr' => ['class' => 'mb-3']])
            ->add('roles', ChoiceType::class,
                ['multiple' => true, 'choices' => ['Super admin' => 'ROLE_SUPERADMIN'], 'attr' => ['class' => 'mb-3']])
            ->add('save', SubmitType::class, ['label' => 'Submit'])
            ->getForm();
    }

    /**
     * @Route("/addUser", name="admin_add_user")
     * @param Request $request
     * @return Response
     */
    public function addUser(Request $request): Response
    {
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

        return $this->render(
            'admin/users/add_user.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/editUser/{id}", name="admin_edit_user")
     * @param Request $request
     * @param User    $user
     * @return Response
     */
    public function editUser(Request $request, User $user): Response
    {
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

        return $this->render(
            'admin/users/edit_user.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/delUser", name="admin_del_user")
     * @param Request $request
     * @return Response
     */
    public function deleteUser(Request $request): Response
    {
        if ($request->request->has('userid')) {
            $user = $this->userRepository->find($request->request->get('userid'));
            if ($user) {
                $manager = $this->getDoctrine()->getManager();
                $manager->remove($user);
                $manager->flush();
                $this->addFlash('success', sprintf('User "%s" removed!', $user->getUsername()));
            } else {
                $this->addFlash('error', sprintf('User "%s" not found.', $user->getUsername()));
            }
        } else {
            $this->addFlash('error', "User is was not sent with request.");
        }

        return $this->redirectToRoute('admin_users');
    }
}
