<?php
namespace App\Security;

use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\ControllerServices\RedisService;
use Doctrine\Persistence\ManagerRegistry;

class CachedUserProvider extends EntityUserProvider
{
    private RedisService $redisService;

    public function __construct(
        ManagerRegistry $registry,
        RedisService $redisService,
        string $userClass, 
    ){
        parent::__construct($registry, $userClass);
        $this->redisService = $redisService;
    }

    public function loadUserByUsername(string $username)
    {
        $cacheKey = 'user' . md5($username);

        $cachedUser = $this->redisService->get($cacheKey);
        if ($cachedUser) {
            return unserialize($cachedUser);
        }

        // Si l'utilisateur n'est pas dans le cache, on le charge depuis la base de données
        $user = parent::loadUserByUsername($username);
        $serializedUser = serialize($user);
        $this->redisService->set($cacheKey, $serializedUser);

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        $cacheKey = 'user' . md5($user->getEmail());

        $cachedUser = $this->redisService->get($cacheKey);
        if ($cachedUser) {
            try {
                $userObject = unserialize($cachedUser);
                if (!$userObject instanceof UserInterface) {
                    throw new \Exception('Unserialized object is not of type UserInterface');
                }
                return $userObject;
            } catch (\Exception $e) {
                return $this->getFreshUser($user);
            }
        }

        $freshUser = parent::refreshUser($user);
        $this->redisService->set($cacheKey, serialize($freshUser));

        return $freshUser;
    }

    private function getFreshUser(UserInterface $user): UserInterface
    {
        // Get the fresh user from the database (via parent method)
        $freshUser = parent::refreshUser($user);

        // Serialize the fresh user before saving it to Redis
        $this->redisService->set('user' . md5($user->getEmail()), serialize($freshUser));

        return $freshUser;
    }

    public function supportsClass(string $class): bool
    {
        return parent::supportsClass($class);
    }
}
