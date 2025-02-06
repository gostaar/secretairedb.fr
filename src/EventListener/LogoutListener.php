<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogoutListener implements EventSubscriberInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    // Méthode appelée lors de l'événement de déconnexion
    public function onLogout(LogoutEvent $event): void
    {
        $token = $event->getToken();
        // if ($token && $token->getUser()) {
        //     $this->clearCacheOnLogout($token->getUser());
        // }
    }

    // private function clearCacheOnLogout($user): void
    // {
    //     $this->redisService->delete('staticData_' . md5($user->getId()));
    //     $userDataKeys = $this->redisService->keys('user*');
    //     foreach ($userDataKeys as $key) {
    //         $this->redisService->delete($key);
    //     }
    //     $dynamicDataKeys = $this->redisService->keys('dynamicData_*');
    //     foreach ($dynamicDataKeys as $key) {
    //         $this->redisService->delete($key);
    //     }
    //     $sfKeys = $this->redisService->keys('sf_*');
    //     foreach ($sfKeys as $key) {
    //         $this->redisService->delete($key);
    //     }

    //     // Supprimer d'autres caches dynamiques si nécessaire
    //     // Exemple :
    //     // $this->redisService->del('dynamicData_' . md5($user->getId()));
    // }

    // Retourne les événements auxquels ce listener s'abonne
    public static function getSubscribedEvents(): array
    {
        // S'abonner à l'événement de déconnexion
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }
}
