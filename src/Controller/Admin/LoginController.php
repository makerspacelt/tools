<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class LoginController extends Controller {

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils) {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // TODO: išsiaiškinti, kodėl kartais neveikia šitas, ir matchina routą / vietoje nukreipimo į admin page
            return $this->redirectToRoute('admin_homepage');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('admin/login.html.twig', array('error' => $error));
    }

}
