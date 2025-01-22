<?php

namespace App\Repository;

use App\Entity\Dossier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\SearchData;

/**
 * @extends ServiceEntityRepository<Dossier>
 */
class DossierRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dossier::class);
    }

    public function findServiceByDossierId(int $dossierId)
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.services', 's')
            ->andWhere('d.id = :dossierId')
            ->setParameter('dossierId', $dossierId)
            ->select('s.name')
            ->getQuery()
            ->getSingleResult(); 
    }

    public function findBySearch(SearchData $searchData, $user, $dossiers): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r IN (:dossiers)')
            ->leftJoin('r.documents', 'd') 
            ->leftJoin('d.images', 'i')
            ->leftJoin('r.repertoires', 's')
            ->leftJoin('s.contacts', 'c')
            ->andWhere('r.user = :user_id')
            ->setParameter('dossiers', $dossiers->toArray())
            ->setParameter('user_id', $user->getId());
            
        if (!empty($searchData->q)) {
            $qb->andWhere('r.name LIKE :q OR d.name LIKE :q OR i.slug LIKE :q OR s.nom LIKE :q OR c.nom LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }

        return $qb->getQuery()->getResult();
    }

    public function getUserDossiers(int $userId, string $serviceName)
    {
        return $this->createQueryBuilder('d')
            ->join('d.user', 'u')
            ->andWhere('u.id = :userId')  
            ->setParameter('userId', $userId)
            ->join('d.services', 's')
            ->andWhere('s.name = :serviceName')
            ->setParameter('serviceName', $serviceName)  
            ->getQuery()
            ->getResult(); 
    }

    //    /**
    //     * @return Dossier[] Returns an array of Dossier objects
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

    //    public function findOneBySomeField($value): ?Dossier
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
