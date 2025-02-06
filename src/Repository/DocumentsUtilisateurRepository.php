<?php

namespace App\Repository;

use App\Entity\DocumentsUtilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\SearchData;


/**
 * @extends ServiceEntityRepository<DocumentsUtilisateur>
 */
class DocumentsUtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentsUtilisateur::class);
    }

    /**
     * Récupérer les documents associés à un dossier donné par son ID
     */
    public function findByDossierId(int $dossierId)
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.dossier', 'd')
            ->andWhere('d.id = :dossierId')
            ->setParameter('dossierId', $dossierId)
            ->getQuery()
            ->getResult();
    }

    public function findBySearch(SearchData $searchData, $user, $dossierId): array
    {
        // Création du query builder
        $qb = $this->createQueryBuilder('r')
            ->innerJoin('r.dossier', 'd') 
            ->leftJoin('r.images', 'i') 
            ->where('d.id = :dossierId')  // Filtrer par dossier
            ->andWhere('r.user = :user_id')  // Filtrer par utilisateur
            ->setParameter('user_id', $user->getId())
            ->setParameter('dossierId', $dossierId);

        // Si un critère de recherche est fourni
        if (!empty($searchData->q)) {
            $qb->andWhere('r.name LIKE :q OR i.slug LIKE :q')
                ->setParameter('q', "%" . $searchData->q . "%");
        }

        return $qb->getQuery()->getResult();
    }

    public function getUserDossierDocument($userId, int $dossierId)
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.dossier', 'd')
            ->andWhere('d.id = :dossierId')
            ->setParameter('dossierId', $dossierId)
            ->innerJoin('r.user', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();    
    }

    // public function findDocument($documentId)
    // {
    //     return $this->createQueryBuilder('d')
    //     ->leftJoin('d.images', 'i')  // Jointure avec les images
    //     ->addSelect('i')             // Sélection des images en plus du document
    //     ->where('d.id = :id')
    //     ->setParameter('id', $documentId)
    //     ->getQuery()
    //     ->getOneOrNullResult();
    // }

    //    /**
    //     * @return DocumentsUtilisateur[] Returns an array of DocumentsUtilisateur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?DocumentsUtilisateur
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
