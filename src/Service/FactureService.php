<?php
namespace App\Service;

use App\Entity\Facture;
use App\Entity\FactureLigne;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class FactureService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFacture($id)
    {
        $facture = $this->em->getRepository(Facture::class)->find($id);

        if (!$facture) {
            return null;
        }
    
        $factureData = [
            'id' => $facture->getId(),
            'montant' => number_format($facture->getMontant()) . ' €',
            'date_paiement' => $facture->getDatePaiement()->format('d-m-Y'),
            'date_facture' => $facture->getDateFacture()->format('d-m-Y'),
            'status' => $facture->getStatus(),
            'client' => $facture->getClient(),
            'commentaire' => $facture->getCommentaire(),
            'is_active' => $facture->isActive(),
            'factureLignes' => array_map(function($ligne) {
                return [
                    'designation' => $ligne->getDesignation(),
                    'quantite' => $ligne->getQuantite(),
                    'prixUnitaire' => number_format($ligne->getPrixUnitaire()) . ' €',
                    'htva' => number_format($ligne->getQuantite() * $ligne->getPrixUnitaire(), 2, ',', ' ') . ' €'
                ];
            }, $facture->getFactureLignes()->toArray()) 
        ];
    
        return $factureData;
    }

    public function addFacture(Facture $facture, User $user)
    {
        $existingFacture = $this->em
            ->getRepository(Facture::class)
            ->findOneBy(['id' => $facture->getId(), 'client' => $user]);

        if ($existingFacture) {
            return false;
        }

        $user->addFacture($facture);
        $this->em->persist($facture);
        $this->em->flush();

        $this->addFlash('success', 'la facture a été créée');

        return true;
    }

    public function addFactureLigne(Facture $facture, FactureLigne $factureLigne): void
    {
        $factureLigne->setFacture($facture);

        $this->entityManager->persist($factureLigne);
        $this->entityManager->flush();
    }

    public function deleteFacture(int $id, User $user)
    {
        $existingFacture = $this->em
            ->getRepository(Facture::class)
            ->findOneBy(['id' => $id, 'client' => $user]);

        if (!$existingFacture) {
            return false; 
        }

        $this->em->remove($existingFacture);
        $this->em->flush();

        return true; 
    }

    public function updateFacture(
        int $id, 
        User $user, 
        ?float $montant, 
        ?DateTimeInterface $date_facture, 
        ?DateTimeInterface $date_paiement, 
        ?string $status, 
        ?string $commentaire, 
        ?bool $is_active
    ){
        $existingFacture = $this->em
            ->getRepository(Facture::class)
            ->findOneBy(['id' => $id, 'client' => $user]);

        if (!$existingFacture) {
            return false; 
        }

        if ($name !== null) {
            $existingFacture->setMontant($montant);
            $existingFacture->setDatePaiement($date_facture);
            $existingFacture->setDateFacture($date_paiement);
            $existingFacture->setStatus($status);
            $existingFacture->setCommentaire($commentaire);
            $existingFacture->setActive($is_active);
        }

        $this->em->flush();

        return true; 
    }
}