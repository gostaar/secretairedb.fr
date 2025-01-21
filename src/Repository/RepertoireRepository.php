<?php

namespace App\Repository;

use App\Entity\Repertoire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\SearchData;

/**
 * @extends ServiceEntityRepository<Repertoire>
 */
class RepertoireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Repertoire::class);
    }

    /**
     * Récupérer les répertoires associés à un dossier donné par son ID
     */
    public function findByDossierId(int $dossierId, $user)
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.dossier', 'd') 
            ->andWhere('d.id = :dossierId')
            ->andWhere('r.user = :user_id')
            ->setParameter('dossierId', $dossierId)
            ->setParameter('user_id', $user->getId())
            ->getQuery()
            ->getResult();
    }

    public function findBySearch(SearchData $searchData, $user, $dossierId): array
    {
        // Création du query builder
        $qb = $this->createQueryBuilder('r')
            ->innerJoin('r.dossier', 'd') 
            ->leftJoin('r.contacts', 'c') 
            ->andWhere('d.id = :dossierId')  // Filtrer par dossier
            ->andWhere('r.user = :user_id')  // Filtrer par utilisateur
            ->setParameter('user_id', $user->getId())
            ->setParameter('dossierId', $dossierId);

        // Si un critère de recherche est fourni
        if (!empty($searchData->q)) {
            $qb->andWhere('r.nom LIKE :q OR c.nom LIKE :q')
                ->setParameter('q', "%" . $searchData->q . "%");
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Repertoire[] Returns an array of Repertoire objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Repertoire
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
