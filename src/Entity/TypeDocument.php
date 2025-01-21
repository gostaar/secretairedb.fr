<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TypeDocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeDocumentRepository::class)]
#[ApiResource]
class TypeDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, DocumentsUtilisateur>
     */
    #[ORM\OneToMany(targetEntity: DocumentsUtilisateur::class, mappedBy: 'typeDocument')]
    private Collection $documents;

    #[ORM\ManyToOne(inversedBy: 'typeDocuments')]
    private ?User $user = null;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    public function __toString()
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
            $document->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocument(DocumentsUtilisateur $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getTypeDocument() === $this) {
                $document->setTypeDocument(null);
            }
        }

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
}
