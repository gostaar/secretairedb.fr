<?php
namespace App\DataFixtures;

use App\Entity\Services;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory as FakerFactory;

class ServicesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $userRepository = $manager->getRepository(User::class);
        $users = $userRepository->loadUsersRoles();

        foreach (Services::AVAILABLE_SERVICES as $serviceName) {
            $service = new Services();
            $service->setName($serviceName);

            // Ajouter tous les utilisateurs à ce service
            foreach ($users as $user) {
                $service->addUser($user);
            }

            $manager->persist($service);
        }
    
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class, 
        ];
    }
}
