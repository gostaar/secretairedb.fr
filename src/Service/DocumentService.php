<?php
namespace App\Service;

use App\Entity\DocumentsUtilisateur;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\TypeDocument;
use App\Entity\Dossier;
use App\Entity\Services;

class DocumentService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getDocument($id)
    {
        return $this->em->getRepository(DocumentsUtilisateur::class)->find($id);
    }

    public function addDocumentsUtilisateur(DocumentsUtilisateur $document, User $user)
    {
        $existingDocumentsUtilisateur = $this->em
            ->getRepository(DocumentsUtilisateur::class)
            ->findOneBy(['name' => $document->getName(), 'user' => $user]);

        if ($existingDocumentsUtilisateur) {
            return false;
        }

        $user->addDocumentsUtilisateur($document);
        $this->em->persist($document);
        $this->em->flush();

        return true;
    }

        public function deleteDocument(int $id, User $user)
    {
        $existingDocument = $this->em
            ->getRepository(DocumentsUtilisateur::class)
            ->findOneBy(['id' => $id, 'user' => $user]);

        if (!$existingDocument) {
            return false; 
        }

        $this->em->remove($existingDocument);
        $this->em->flush();

        return true; 
    }

    public function updateDocument(
        int $id, 
        User $user, 
        ?TypeDocument $type_document, 
        ?DateTimeInterface $date_document, 
        ?Dossier $dossier, 
        ?string $details, 
        ?string $expediteur, 
        ?string $destinataire,
        ?Services $service,
        ?string $file_path,
        ?bool $is_active
    ){
        $existingDocument = $this->em
            ->getRepository(DocumentsUtilisateur::class)
            ->findOneBy(['id' => $id, 'user' => $user]);

        if (!$existingDocument) {
            return false; 
        }

        if ($name !== null) {
            $existingDocument->setTypeDocument($type_document);
            $existingDocument->setDateDocument($date_Document);
            $existingDocument->setDossier($dossier);
            $existingDocument->setDetails($details);
            $existingDocument->setExpediteur($expediteur);
            $existingDocument->setDestinataire($destinataire);
            $existingDocument->setService($service);
            $existingDocument->setFilePath($destinataire);
            $existingDocument->setIsActive($is_active);
        }

        $this->em->flush();

        return true; 
    }
}