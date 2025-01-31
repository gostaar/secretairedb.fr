<?php

namespace App\Repository;

use App\Entity\Identifiants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Identifiants>
 */
class IdentifiantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Identifiants::class);
    }

    public function getUserIdentifiants(int $userId)
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.users', 'u')
            ->where('u.id = :userId')   // Filtrage sur l'id de l'utilisateur
            ->setParameter('userId', $userId)
            ->getQuery() 
            ->getResult(); 
    }

    //    /**
    //     * @return Identifiants[] Returns an array of Identifiants objects
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

    //    public function findOneBySomeField($value): ?Identifiants
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
