<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{   
    private RouterInterface $router;

    public function __construct(
        RouterInterface $router
    ){
        $this->router = $router;
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $roles = $token->getRoleNames();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            return new RedirectResponse('/admin');
        }

        if (in_array('ROLE_USER', $roles, true)) {
            return $this->handleUserRedirection($token);
        }

        return new RedirectResponse($this->router->generate('app_login'));
    }

    private function handleUserRedirection(TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        return new RedirectResponse($this->router->generate('user'));
    }

}