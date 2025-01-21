<?php 
namespace App\DataFixtures;

use App\Entity\TypeDocument;
use App\Entity\DocumentsUtilisateur;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TypeDocumentFixtures extends Fixture 
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();
        $userRepository = $manager->getRepository(User::class);
        $users = $userRepository->loadUsersRoles();

        $typeDocument1 = new TypeDocument();
        $typeDocument1->setName('Type Document 1');
        $typeDocument1->setUser($faker->randomElement($users));
        $manager->persist($typeDocument1);

        $typeDocument2 = new TypeDocument();
        $typeDocument2->setName('Type Document 2');
        $typeDocument1->setUser($faker->randomElement($users));
        $manager->persist($typeDocument2);

        $typeDocument3 = new TypeDocument();
        $typeDocument3->setName('Type Document 3');
        $typeDocument1->setUser($faker->randomElement($users));
        $manager->persist($typeDocument3);

        $manager->flush();
    }

}
