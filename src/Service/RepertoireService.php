<?php
namespace App\Service;

use App\Entity\Repertoire;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Dossier;
use App\Entity\Contact;

class RepertoireService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRepertoire($id)
    {
        return $this->em->getRepository(Repertoire::class)->find($id);
    }

    public function addRepertoire(Repertoire $repertoire, User $user)
    {
        $existingRepertoire = $this->em
            ->getRepository(Repertoire::class)
            ->findOneBy(['nom' => $repertoire->getNom(), 'user' => $user]);

        if ($existingRepertoire) {
            return false;
        }

        $user->addRepertoire($repertoire);
        $this->em->persist($repertoire);
        $this->em->flush();

        return true;
    }

    public function deleteRepertoire(int $id, User $user)
    {
        $existingRepertoire = $this->em
            ->getRepository(Repertoire::class)
            ->findOneBy(['id' => $id, 'user' => $user]);

        if (!$existingRepertoire) {
            return false; 
        }

        $this->em->remove($existingRepertoire);
        $this->em->flush();

        return true; 
    }

    public function updateRepertoire(
        int $id, 
        User $user, 
        ?string $nom,
        ?string $adresse, 
        ?string $code_postal, 
        ?string $ville, 
        ?string $pays,
        ?string $telephone, 
        ?string $mobile, 
        ?string $email,
        ?string $siret,
        ?string $nom_entreprise,
        ?Dossier $dossier,
        ?Contact $contact
    ){
        $existingRepertoire = $this->em
            ->getRepository(Repertoire::class)
            ->findOneBy(['id' => $id, 'user' => $user]);

        if (!$existingRepertoire) {
            return false; 
        }

        if ($name !== null) {
            $existingRepertoire->setNom($nom);
            $existingRepertoire->setAdresse($adresse);
            $existingRepertoire->setCodePostal($code_postal);
            $existingRepertoire->setVille($ville);
            $existingRepertoire->setPays($pays);
            $existingRepertoire->setTelephone($telephone);
            $existingRepertoire->setMobile($mobile);
            $existingRepertoire->setEmail($email);
            $existingRepertoire->setSiret($siret);
            $existingRepertoire->setNomEntreprise($nom_entreprise);
            $existingRepertoire->setDossier($dossier);
            $existingRepertoire->addRepertoireContact($contact);

        }

        $this->em->flush();

        return true; 
    }
}