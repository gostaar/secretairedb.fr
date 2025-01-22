<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use App\Service\ControllerServices\RouteDataService;
use App\Service\ControllerServices\RedisService;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{   
    private RouteDataService $routeDataService;
    private RedisService $redisService;
    private RouterInterface $router;

    public function __construct(
        RouteDataService $routeDataService,
        RedisService $redisService,
        RouterInterface $router
    ){
        $this->routeDataService = $routeDataService;
        $this->redisService = $redisService;
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

        try {
            $user->initializeRelations();
            $userSerialized = serialize($user);
            $this->redisService->set("user".md5($user->getEmail()), $userSerialized);
            $staticData = $this->routeDataService->getStaticData($user, $fragment = null);
            $staticDataCacheKey = 'staticData_' . md5($user->getId());
            $this->redisService->set($staticDataCacheKey, serialize($staticData));
        } catch (\Throwable $e) {
            $this->logger->error('Erreur lors de la gestion des données utilisateur : '.$e->getMessage());
        }

        return new RedirectResponse($this->router->generate('user'));
    }

}