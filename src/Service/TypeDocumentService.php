<?php
namespace App\Service;

use App\Entity\TypeDocument;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TypeDocumentService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getTypeDocument($id)
    {
        return $this->em->getRepository(TypeDocument::class)->find($id);
    }

    public function addTypeDocument(TypeDocument $TypeDocument, User $user)
    {
        $existingTypeDocument = $this->em
            ->getRepository(TypeDocument::class)
            ->findOneBy(['name' => $TypeDocument->getName(), 'user' => $user]);

        if ($existingTypeDocument) {
            return false;
        }
        $user->addDossier($TypeDocument);
        $this->em->persist($TypeDocument);
        $this->em->flush();

        return true;
    }

    public function deleteTypeDocument(int $id, User $user)
    {
        $existingTypeDocument = $this->em
            ->getRepository(TypeDocument::class)
            ->findOneBy(['id' => $id, 'user' => $user]);

        if (!$existingTypeDocument) {
            return false; 
        }

        $this->em->remove($existingTypeDocument);
        $this->em->flush();

        return true; 
    }

    public function updateTypeDocument(int $id, User $user, ?string $name)
    {
        $existingTypeDocument = $this->em
            ->getRepository(TypeDocument::class)
            ->findOneBy(['id' => $id, 'user' => $user]);

        if (!$existingTypeDocument) {
            return false; 
        }

        if ($name !== null) {
            $existingTypeDocument->setName($name);
        }

        $this->em->flush();

        return true; 
    }
}
