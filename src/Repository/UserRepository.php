<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
// implements PasswordUpgraderInterface
{
    private EntityManagerInterface $_em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $_em)
    {
        parent::__construct($registry, User::class);
        $this->_em = $_em;
    }

    public function updatePassword(User $user, string $hashedPassword): void
    {
        $user = $this->_em->getRepository(User::class)->find($user->getId());

        if (!$user) {
            throw new \Exception('Utilisateur non trouvé.');
        }

        // Mettre à jour uniquement le mot de passe
        $user->setPassword($hashedPassword);
        $user->setPasswordResetToken(null);
        $user->setPasswordResetExpiresAt(new \DateTime('+1 hour'));

        // Sauvegarder les modifications
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function getUserId(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return new Response('User not authenticated', Response::HTTP_UNAUTHORIZED);
        }

        $userId = $user->getId();

        return new Response($userId);
    }

    public function loadUserByIdentifier(string $username): ?User
    {
        return $this->findOneBy(['email' => $username]); // Adjust this if you're using a different field for login
    }

    public function loadUsersRoles(): array
    {
        $users = $this->createQueryBuilder('u')
            ->getQuery()
            ->getResult();

        return array_filter($users, function ($user) {
            return !in_array('ROLE_ADMIN', $user->getRoles(), true);
        });
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
