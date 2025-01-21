<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\DevisStatus;
use App\Repository\DevisVersionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisVersionRepository::class)]
#[ApiResource]
class DevisVersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $montant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    #[ORM\Column(enumType: DevisStatus::class)]
    private ?DevisStatus $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_modification = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $version = null;

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

    public function setActive(bool $is_active): static
    {
        $this->is_active = $is_active;

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

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->date_modification;
    }

    public function setDateModification(\DateTimeInterface $date_modification): static
    {
        $this->date_modification = $date_modification;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): static
    {
        $this->version = $version;

        return $this;
    }
}
