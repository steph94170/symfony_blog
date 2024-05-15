<?php
namespace App\Controller\Visitor\Authentication;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'visitor_authentication_login', methods:['GET','POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est deja connecté,
        // il n'a plus rien a faire sur la page de connexion
        // Redirigeons-le vers la page d'accueil 
        if ($this->getUser()) {
            return $this->redirectToRoute('visitor_welcome_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // 1-  Afficher la page de connexion
        return $this->render('pages/visitor/authentication/login.html.twig', [
            'last_username' => $lastUsername, 'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
