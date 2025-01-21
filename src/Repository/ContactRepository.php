<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\SearchData;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
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
    //     * @return Contact[] Returns an array of Contact objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Contact
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
