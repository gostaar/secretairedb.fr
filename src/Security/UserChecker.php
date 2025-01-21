<?php
namespace App\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        // Exemple : vérifier si l'utilisateur est actif
        // if (!$user->isActive()) {
        //     throw new CustomUserMessageAccountStatusException('Votre compte est désactivé.');
        // }
        if (!$user) {
            throw new CustomUserMessageAccountStatusException('Vous n\'existez pas dans la base de données.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Ajoutez ici les vérifications après l'authentification si nécessaire
    }
}
