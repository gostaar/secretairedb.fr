<?php

namespace App\Repository;

use App\Entity\Services;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Services>
 */
class ServicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Services::class);
    }

    public function getUserServices(int $userId)
    {
        return $this->createQueryBuilder('s')
            ->join('s.users', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult(); 
    }

    public function getServiceByName(string $serviceName): ?\App\Entity\Services
    {
        // Création de la requête pour récupérer le service par son nom
        $service = $this->createQueryBuilder('s')
            ->select('s')  // Sélectionner tous les champs du service
            ->where('s.name = :serviceName')  // Filtrer par le nom du service
            ->setParameter('serviceName', $serviceName)  // Fixer le paramètre pour la requête
            ->getQuery()
            ->getOneOrNullResult();  // Récupérer un seul résultat ou null si aucun service trouvé

        return $service;  // Retourner l'objet Service ou null si non trouvé
    }

    //    /**
    //     * @return Services[] Returns an array of Services objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Services
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
