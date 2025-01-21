<?php 
namespace App\Security;

use App\Entity\User;

interface OwnedEntityInterface
{
    public function getUser(): ?User;
}