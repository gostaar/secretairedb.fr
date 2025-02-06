<?php

namespace App\Repository;

use App\Entity\GoogleToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GoogleToken>
 */
class GoogleTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoogleToken::class);
    }

    public function findValidTokenByUser(\App\Entity\User $user): ?GoogleToken
    {
        $qb = $this->createQueryBuilder('gt')
            ->where('gt.users = :user')
            ->setParameter('user', $user)
            ->orderBy('gt.createdAt', 'DESC') // En cas de plusieurs tokens, on prend le plus récent
            ->setMaxResults(1);

        $token = $qb->getQuery()->getOneOrNullResult();

        if ($token) {
            $expirationTime = $token->getCreatedAt()->getTimestamp() + 3600;
            $currentTime = (new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')))->getTimestamp();

            return ($expirationTime > $currentTime) ? $token : null;
        }

        return null;
    }

    //    /**
    //     * @return GoogleToken[] Returns an array of GoogleToken objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GoogleToken
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
