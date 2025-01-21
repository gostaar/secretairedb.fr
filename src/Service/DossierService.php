<?php
namespace App\Service;

use App\Entity\Dossier;
use App\Entity\User;
use App\Entity\Services;
use Doctrine\ORM\EntityManagerInterface;

class DossierService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getDossier($id)
    {
        return $this->em->getRepository(Dossier::class)->find($id);
    }

    public function addDossier(Dossier $dossier, User $user)
    {
        $existingDossier = $this->em
            ->getRepository(Dossier::class)
            ->findOneBy(['name' => $dossier->getName(), 'user' => $user, 'services' => $dossier->getServices()]);

        if ($existingDossier) {
            return false;
        }

        
        $user->addDossier($dossier);
        $this->em->persist($dossier);
        $this->em->flush();

        return true;
    }

    public function deleteDossier(int $id, User $user)
    {
        $existingDossier = $this->em
            ->getRepository(Dossier::class)
            ->findOneBy(['id' => $id, 'user' => $user]);

        if (!$existingDossier) {
            return false; 
        }

        $this->em->remove($existingDossier);
        $this->em->flush();

        return true; 
    }

    public function updateDossier(int $id, User $user, ?string $name)
    {
        $existingDossier = $this->em
            ->getRepository(Dossier::class)
            ->findOneBy(['id' => $id, 'user' => $user]);

        if (!$existingDossier) {
            return false; 
        }

        if ($name !== null) {
            $existingDossier->setName($name);
        }

        $this->em->flush();

        return true; 
    }
}
