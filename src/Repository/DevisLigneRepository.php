<?php

namespace App\Repository;

use App\Entity\DevisLigne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\SearchData;

/**
 * @extends ServiceEntityRepository<DevisLigne>
 */
class DevisLigneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevisLigne::class);
    }

    public function findByDevis($devisId, $user):array 
    {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.devis', 'd') 
            ->where('d.id = :devisId')  // Filtrer par Devis
            ->setParameter('devisId', $devisId)
            ->getQuery()
            ->getResult();
    }

    public function findBySearch(SearchData $searchData, $user, $devisId): array
    {
        $qb = $this->createQueryBuilder('l')
            ->innerJoin('l.devis', 'd')
            ->where('d.id = :devisId')
            ->andWhere('d.client = :user_id')
            ->setParameter('user_id', $user->getId())
            ->setParameter('devisId', $devisId);

        if (!empty($searchData->q)) {
            $qb->andWhere('d.id = :q') 
                ->setParameter('q', $searchData->q );
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return DevisLigne[] Returns an array of DevisLigne objects
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

    //    public function findOneBySomeField($value): ?DevisLigne
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
