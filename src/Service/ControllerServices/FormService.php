<?php 

namespace App\Service\ControllerServices;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Model\SearchData;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormService extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function searchInFile(SearchData $entity, User $user, $serviceName): array
    {
        $service = $this->entityManager->getRepository(\App\Entity\Services::class)->getServiceByName($serviceName);
        $service->initializeRelations();

        return $this->entityManager
            ->getRepository(\App\Entity\Dossier::class)
            ->findBySearch($entity, $user, $service->getDossiers());
    }

    public function searchInDocument(SearchData $entity, User $user, ?int $dossierId): array
    {
        return $this->entityManager
            ->getRepository(\App\Entity\DocumentsUtilisateur::class)
            ->findBySearch($entity, $user, $dossierId);
    }

    public function searchInRepository(SearchData $entity, User $user, ?int $dossierId): array
    {
        return $this->entityManager
            ->getRepository(\App\Entity\Repertoire::class)
            ->findBySearch($entity, $user, $dossierId);
    }
}
