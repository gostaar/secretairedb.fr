<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PaiementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
#[ApiResource]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montant_paye = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_paiement = null;

    #[ORM\ManyToOne(inversedBy: 'paiements')]
    private ?Facture $facture = null;

    public function __toString()
    {
        return 'Paiement n° '.$this->id.' '.number_format($this->montant_paye, 2, ',', ' ') . ' €';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantPaye(): ?string
    {
        return $this->montant_paye;
    }

    public function setMontantPaye(string $montant_paye): static
    {
        $this->montant_paye = $montant_paye;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(\DateTimeInterface $date_paiement): static
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): static
    {
        $this->facture = $facture;

        return $this;
    }
}
