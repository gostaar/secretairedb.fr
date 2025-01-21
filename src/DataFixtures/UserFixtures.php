<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory as FakerFactory;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();
$faker->unique(true);

        $user1 = new User();
        $user1->setEmail('user@example.com');
        $user1->setPassword('$2y$13$MkziOVBEic16qAAUshwPeeXFtgDLL88Ca.d9JhPQgyvh.JJDgYY1S');
        $user1->setRoles(['ROLE_USER']);
        $user1->setNom($faker->name);
        $user1->setAdresse($faker->address);
        $user1->setCodePostal($faker->postcode);
        $user1->setVille($faker->city);
        $user1->setPays($faker->country);
        $user1->setTelephone($faker->phoneNumber);
        $user1->setMobile($faker->phoneNumber);
        $user1->setSiret($faker->numberBetween(1000000000, 9999999999));
        $user1->setNomEntreprise($faker->company);

        $manager->persist($user1);
// $manager->flush();
        $user2 = new User();
        $user2->setEmail('user2@example.com');
        $user2->setPassword('$2y$13$Qd7ZjIlwT/2VSbymiLWv0OSINfjNb4mZbFV5oN/rHeyO5Hhwb6iES'); 
        $user2->setRoles(['ROLE_USER']);
        $user2->setNom($faker->name);
        $user2->setAdresse($faker->address);
        $user2->setCodePostal($faker->postcode);
        $user2->setVille($faker->city);
        $user2->setPays($faker->country);
        $user2->setTelephone($faker->phoneNumber);
        $user2->setMobile($faker->phoneNumber);
        $user2->setSiret($faker->numberBetween(1000000000, 9999999999));
        $user2->setNomEntreprise($faker->company);

        $manager->persist($user2);
// $manager->flush();
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setNom($faker->name);
        $admin->setAdresse($faker->address);
        $admin->setCodePostal($faker->postcode);
        $admin->setVille($faker->city);
        $admin->setPays($faker->country);
        $admin->setTelephone($faker->phoneNumber);
        $admin->setMobile($faker->phoneNumber);
        $admin->setSiret($faker->numberBetween(1000000000, 9999999999));
        $admin->setNomEntreprise($faker->company);

        $manager->persist($admin);
        $manager->flush();
    }
}
