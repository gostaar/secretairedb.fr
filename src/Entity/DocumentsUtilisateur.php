<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DocumentsUtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: DocumentsUtilisateurRepository::class)]
#[ApiResource(
    shortName: "Documents",
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            //controller: \App\Controller\User\DocumentsUtilisateurController::class . '::new',
        ),
        new Patch(),
        new Delete(),
    ]
)]
class DocumentsUtilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date ne peut pas être vide.")]
    private ?\DateTimeInterface $date_document = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $expediteur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destinataire = null;

    #[ORM\Column]
    private ?bool $is_active = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $details = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?Dossier $dossier = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?TypeDocument $typeDocument = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'document')]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'date_document' => $this->getDateDocument(),
            'name' => $this->getName(),
            'expediteur' => $this->getExpediteur(),
            'destinataire' => $this->getDestinataire(),
            'file_path' => $this->getFilePath(),
            'is_active' => $this->isActive(),
            'details' => $this->getDetails(),
            'user' => $this->getUser(),
            'dossier' => $this->getDossier(),
            'typeDocument' => $this->getTypeDocument(),
        ];
    }

    public function __toString(): string
    {
        return $this->name;
    }   


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDocument(): ?\DateTimeInterface
    {
        return $this->date_document;
    }

    public function setDateDocument(\DateTimeInterface $date_document): static
    {
        $this->date_document = $date_document;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getExpediteur(): ?string
    {
        return $this->expediteur;
    }

    public function setExpediteur(?string $expediteur): static
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    public function getDestinataire(): ?string
    {
        return $this->destinataire;
    }

    public function setDestinataire(?string $destinataire): static
    {
        $this->destinataire = $destinataire;

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

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): static
    {
        $this->dossier = $dossier;

        return $this;
    }

    public function getTypeDocument(): ?TypeDocument
    {
        return $this->typeDocument;
    }

    public function setTypeDocument(?TypeDocument $typeDocument): static
    {
        $this->typeDocument = $typeDocument;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setDocuments($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getDocuments() === $this) {
                $image->setDocuments(null);
            }
        }

        return $this;
    }
}
