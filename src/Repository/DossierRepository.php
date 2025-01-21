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
            ->innerJoin('d.services', 's')  // Joindre la table Service
            ->andWhere('d.id = :dossierId')  // Filtrer par l'id du dossier
            ->setParameter('dossierId', $dossierId)
            ->select('s.name')  // Sélectionner uniquement le nom du service
            ->getQuery()
            ->getSingleResult();  // Retourner un seul résultat (le nom du service)
    }

    public function findBySearch(SearchData $searchData, $user, $dossiers): array
    {
        // Créer le query builder pour l'entité Dossier
        $qb = $this->createQueryBuilder('d')
            ->where('d IN (:dossiers)')  // Filtrer par les dossiers spécifiés
            ->andWhere('d.user = :user_id')
            ->setParameter('dossiers', $dossiers->toArray())
            ->setParameter('user_id', $user->getId());
            
        // Ajouter un critère de recherche sur le nom si une requête est définie
        if (!empty($searchData->q)) {
            $qb->andWhere('d.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }

        // Exécuter la requête et retourner les résultats sous forme de tableau
        return $qb->getQuery()->getResult();
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
