<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'visitor_authentication_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        // 2- On va recuperer l'email envoyé de l'utilisateur depuis le formulaire de connexion
        $email = $request->getPayload()->getString('email');

        // 3- sauvegarder l'email en session
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);
       
        // 4- Verifier si l'email et le mot de passe envoyer par le formulaire correspondent a un utilisateur existant dans la base de données

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        // 5- Récupérer l'email précedemment envoyé depuis le formulaire et qui a été sauvegardé en session
         // Effectuer la redirection vers la page de laquelle proviennent les informations.
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // 6- 
        // dans le cas contraire, effectuer une redirection vers la page d'accueil
        // redirigeont l'admistrateur vers l'espace d'administration
        // et l'utilisateur vers la page d'accueil
        $user = $token->getUser() ;
        $roles = $user->getRoles();

        if (\in_array("ROLE_ADMIN", $roles)) 
        {
            return new RedirectResponse($this-> urlGenerator->generate('admin_home')) ;
        }

        if (\in_array("ROLE_USER", $roles)) 
        {
            return new RedirectResponse($this-> urlGenerator->generate('visitor_welcome_index')) ;
        }

    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
