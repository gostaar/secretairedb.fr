<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\HasLifecycleCallbacks()]
#[Vich\Uploadable]
#[ApiResource(
    normalizationContext: ['groups' => ['image:read']],
    denormalizationContext: ['groups' => ['image:write']],
    types: ['https://schema.org/Image'],
    outputFormats: ['jsonld' => ['application/ld+json']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            inputFormats: ['multipart' => ['multipart/form-data']]
        ),
        new Delete(),
    ])
]

class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['image:read'])]
    private ?int $id = null;
    
    #[Vich\UploadableField(mapping: 'image_vich', fileNameProperty: 'imageName', size: 'imageSize')]
    #[Groups(['image:write'])]
    private ?File $imageFile = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['image:write', 'image:read'])]
    private ?string $slug = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['image:read'])]
    private ?string $imageName = null;
    
    #[ORM\Column(nullable: true)]
    #[Groups(['image:read'])]
    private ?int $imageSize = null;
    
    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['image:write', 'image:read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    private ?DocumentsUtilisateur $document = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['image:write', 'image:read'])]
    private ?string $imageDescription = null;

    public function __toString(){
        return $this->imageName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $image = null)
    {
        $this->imageFile = $image;

        return $this;
    }


    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
    
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDocument(): ?DocumentsUtilisateur
    {
        return $this->document;
    }

    public function setDocument(?DocumentsUtilisateur $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getImageDescription(): ?string
    {
        return $this->imageDescription;
    }

    public function setImageDescription(?string $imageDescription): static
    {
        $this->imageDescription = $imageDescription;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(mixed $imageSize): static
    {
        if (is_string($imageSize)) {
            $imageSize = (int) trim($imageSize);
        }

        if (!is_int($imageSize) && $imageSize !== null) {
            throw new \InvalidArgumentException('La taille de l\'image doit être un entier ou null.');
        }

        $this->imageSize = $imageSize;

        return $this;
    }

}
