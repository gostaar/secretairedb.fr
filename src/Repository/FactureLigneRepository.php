<?php

namespace App\Repository;

use App\Entity\FactureLigne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
            ->select('l.quantite * l.prix_unitaire AS totalMontant')  // Calculer le montant total
            ->getQuery()
            ->getResult();
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
