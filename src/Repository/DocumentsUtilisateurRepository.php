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
            ->innerJoin('r.dossier', 'd')  // Assurez-vous que 'dossier' est bien la relation dans l'entité DocumentsUtilisateurs
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
            ->where('d.id = :dossierId')  // Filtrer par dossier
            ->andWhere('r.user = :user_id')  // Filtrer par utilisateur
            ->setParameter('user_id', $user->getId())
            ->setParameter('dossierId', $dossierId);

        // Si un critère de recherche est fourni
        if (!empty($searchData->q)) {
            $qb->andWhere('r.name LIKE :q')
                ->setParameter('q', "%" . $searchData->q . "%");
        }

        return $qb->getQuery()->getResult();
    }

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
