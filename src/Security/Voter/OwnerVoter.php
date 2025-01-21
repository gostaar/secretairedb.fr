<?php
namespace App\Security\Voter;

use App\Security\OwnedEntityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OwnerVoter extends Voter
{
    const VIEW = 'VIEW';
    const EDIT = 'EDIT';

    protected function supports(string $attribute, $subject): bool
    {
        // Vérifie que l'attribut est pris en charge et que le sujet implémente l'interface
        return in_array($attribute, [self::VIEW, self::EDIT], true) 
            && $subject instanceof OwnedEntityInterface;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // Vérifie si l'utilisateur possède l'entité
        return $subject->getUser() === $user;
    }
}
