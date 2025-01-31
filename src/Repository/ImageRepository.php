<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\DocumentsUtilisateur;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Image>
 */
class ImageRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $_em;


    public function __construct(ManagerRegistry $registry, EntityManagerInterface $_em)
    {
        parent::__construct($registry, Image::class);
        $this->_em = $_em;
    }

    public function updateImage(FormInterface $form): void
    {
        // Récupérer le fichier téléchargé
        $imageFile = $form->get('imageFile')->getData();

        // Vérifier si c'est bien un fichier téléchargé
        if ($imageFile instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            $imageSize = $imageFile->getSize(); 
            $imageName = $imageFile->getClientOriginalName(); 
        } else {
            $imageSize = 0;
            $imageName = '';
        }

        // Récupérer les autres champs du formulaire
        $slug = $form->get('slug')->getData();
        $documentId = $form->get('document')->getData();
        $description = $form->get('imageDescription')->getData();
        $imageId = $form->get('id')->getData();

        // Récupérer l'image et le document depuis la base de données
        $image = $this->_em->getRepository(Image::class)->find($imageId);
        $document = $this->_em->getRepository(\App\Entity\DocumentsUtilisateur::class)->find($documentId);

        if ($image) {
            // Mettre à jour les propriétés de l'image
            $image->setImageFile($imageFile);
            $image->setImageSize($imageSize);
            $image->setImageName($imageName);
            $image->setSlug($slug);
            $image->setDocument($document);  // Assigner le document récupéré
            $image->setImageDescription($description);

            // Persister l'image mise à jour
            $this->_em->persist($image);
            $this->_em->flush();
        } else {
            throw new \Exception("Image non trouvée.");
        }
    }

//    /**
//     * @return Image[] Returns an array of Image objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Image
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
