<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\FactureStatus;
use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[ApiResource]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_paiement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_facture = null;

    #[ORM\Column(enumType: FactureStatus::class)]
    private ?FactureStatus $status = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    private ?User $client = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentaire = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    /**
     * @var Collection<int, FactureLigne>
     */
    #[ORM\OneToMany(targetEntity: FactureLigne::class, mappedBy: 'facture')]
    private Collection $factureLignes;

    /**
     * @var Collection<int, Paiement>
     */
    #[ORM\OneToMany(targetEntity: Paiement::class, mappedBy: 'facture', orphanRemoval: true)]
    private Collection $paiements;

    public function __construct()
    {
        $this->factureLignes = new ArrayCollection();
        $this->paiements = new ArrayCollection();
    }

    public function __toString(): string{
        return 'Facture n° '.$this->id.' '.number_format($this->montant, 2, ',', ' ') . ' €';;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'montant' => $this->getMontant(),
            'date_paiement' => $this->getDatePaiement(),
            'date_facture' => $this->getDateFacture(),
            'client' => $this->getClient(),
            'commentaire' => $this->getCommentaire(),
            'is_active' => $this->isActive(),
            'paiements' => $this->getPaiements(),
            'status' => $this->getStatusLabel(),
            'factureLignes' => $this->getFactureLignes(),
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(?\DateTimeInterface $date_paiement): static
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getDateFacture(): ?\DateTimeInterface
    {
        return $this->date_facture;
    }

    public function setDateFacture(?\DateTimeInterface $date_facture): static
    {
        $this->date_facture = $date_facture;

        return $this;
    }

    public function getStatus(): ?FactureStatus
    {
        return $this->status;
    }

    public function getStatusLabel(): string
    {
        return $this->status->value;
    }

    public function setStatus(FactureStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setActive(bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }

    /**
     * @return Collection<int, FactureLigne>
     */
    public function getFactureLignes(): Collection
    {
        return $this->factureLignes;
    }

    public function addFactureLigne(FactureLigne $factureLigne): static
    {
        if (!$this->factureLignes->contains($factureLigne)) {
            $this->factureLignes->add($factureLigne);
            $factureLigne->setFacture($this);
        }

        return $this;
    }

    public function removeFactureLigne(FactureLigne $factureLigne): static
    {
        if ($this->factureLignes->removeElement($factureLigne)) {
            // set the owning side to null (unless already changed)
            if ($factureLigne->getFacture() === $this) {
                $factureLigne->setFacture(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): static
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements->add($paiement);
            $paiement->setFacture($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getFacture() === $this) {
                $paiement->setFacture(null);
            }
        }

        return $this;
    }
}
