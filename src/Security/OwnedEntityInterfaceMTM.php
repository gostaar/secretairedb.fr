<?php 
namespace App\Security;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;

interface OwnedEntityInterfaceMTM
{
    public function getUsers(): Collection;
}