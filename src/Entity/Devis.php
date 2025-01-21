<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\DevisStatus;
use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
#[ApiResource]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $montant = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_devis = null;

    #[ORM\Column(enumType: DevisStatus::class)]
    private ?DevisStatus $status = null;

    /**
     * @var Collection<int, DevisLigne>
     */
    #[ORM\OneToMany(targetEntity: DevisLigne::class, mappedBy: 'devis', orphanRemoval: true)]
    private Collection $devisLignes;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    private ?User $client = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    public function __construct()
    {
        $this->devisLignes = new ArrayCollection();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'montant' => $this->getMontant(),
            'date_devis' => $this->getDateDevis(),
            'client' => $this->getClient(),
            'commentaire' => $this->getCommentaire(),
            'is_active' => $this->isActive(),
            'status' => $this->getStatusLabel(),
        ];
    }

    public function __toString(): string{
        return 'Devis n° '.$this->id.' '.number_format($this->montant, 2, ',', ' ') . ' €';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(?string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateDevis(): ?\DateTimeImmutable
    {
        return $this->date_devis;
    }

    public function setDateDevis(\DateTimeImmutable $date_devis): static
    {
        $this->date_devis = $date_devis;

        return $this;
    }

    public function getStatus(): ?DevisStatus
    {
        return $this->status;
    }

    public function getStatusLabel(): string
    {
        return $this->status->value;
    }

    public function setStatus(DevisStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, DevisLigne>
     */
    public function getDevisLignes(): Collection
    {
        return $this->devisLignes;
    }

    public function addDevisLigne(DevisLigne $devisLigne): static
    {
        if (!$this->devisLignes->contains($devisLigne)) {
            $this->devisLignes->add($devisLigne);
            $devisLigne->setDevis($this);
        }

        return $this;
    }

    public function removeDevisLigne(DevisLigne $devisLigne): static
    {
        if ($this->devisLignes->removeElement($devisLigne)) {
            // set the owning side to null (unless already changed)
            if ($devisLigne->getDevis() === $this) {
                $devisLigne->setDevis(null);
            }
        }

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

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }
}
