<?php

namespace App\Repository;

use App\Entity\FactureLigne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\SearchData;

/**
 * @extends ServiceEntityRepository<FactureLigne>
 */
class FactureLigneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FactureLigne::class);
    }

    public function findByFacture($factureId, $user):array 
    {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.facture', 'f') 
            ->where('f.id = :factureId')  // Filtrer par facture
            ->setParameter('factureId', $factureId)
            ->getQuery()
            ->getResult();
    }

    public function findBySearch(SearchData $searchData, $user, $factureId): array
    {
        $qb = $this->createQueryBuilder('l')
            ->innerJoin('l.facture', 'f') 
            ->where('f.id = :factureId')
            ->andWhere('f.client = :user_id')
            ->setParameter('user_id', $user->getId())
            ->setParameter('factureId', $factureId);

        if (!empty($searchData->q)) {
            $qb->andWhere('f.id = :q') 
                ->setParameter('q', $searchData->q);
        }

        return $qb->getQuery()->getResult();
    }




//    /**
//     * @return FactureLigne[] Returns an array of FactureLigne objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FactureLigne
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
