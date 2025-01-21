<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DossierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Security\OwnedEntityInterface;

#[ORM\Entity(repositoryClass: DossierRepository::class)]
#[ApiResource(
    security: "is_granted('VIEW', object)",
    operations: [
        new Post(
            controller: \App\Controller\User\DossierController::class . '::new',
            uriTemplate: '/dossiers/{serviceName}'
        ),
        new Patch(
            // controller: \App\Controller\User\DossierController::class . '::edit',
            // uriTemplate: '/dossiers/{dossierId}'
        ),
        new Get(),
        new GetCollection()
    ])
]
class Dossier implements OwnedEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'dossiers')]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    private ?User $user = null;

    /**
     * @var Collection<int, DocumentsUtilisateur>
     */
    #[ORM\OneToMany(targetEntity: DocumentsUtilisateur::class, mappedBy: 'dossier')]
    private Collection $documents;

    /**
     * @var Collection<int, Repertoire>
     */
    #[ORM\OneToMany(targetEntity: Repertoire::class, mappedBy: 'dossier')]
    private Collection $repertoires;

    #[ORM\ManyToOne(inversedBy: 'dossiers')]
    private ?Services $services = null;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->repertoires = new ArrayCollection();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'user' => $this->getUser(),
            'services' => $this->getServices(),
            'repertoires' => $this->getRepertoires(),
            'documents' => $this->getDocuments(),
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, DocumentsUtilisateur>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(DocumentsUtilisateur $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setDossier($this);
        }

        return $this;
    }

    public function removeDocument(DocumentsUtilisateur $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getDossier() === $this) {
                $document->setDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Repertoire>
     */
    public function getRepertoires(): Collection
    {
        return $this->repertoires;
    }

    public function addRepertoire(Repertoire $repertoire): static
    {
        if (!$this->repertoires->contains($repertoire)) {
            $this->repertoires->add($repertoire);
            $repertoire->setDossier($this);
        }

        return $this;
    }

    public function removeRepertoire(Repertoire $repertoire): static
    {
        if ($this->repertoires->removeElement($repertoire)) {
            // set the owning side to null (unless already changed)
            if ($repertoire->getDossier() === $this) {
                $repertoire->setDossier(null);
            }
        }

        return $this;
    }

    public function getServices(): ?Services
    {
        return $this->services;
    }

    public function setServices(?Services $services): static
    {
        $this->services = $services;

        return $this;
    }
}
