<?php

namespace App\DataFixtures;

use App\Entity\Events;
use App\Entity\User;
use App\Entity\Services;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EventsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $userRepository = $manager->getRepository(User::class);
        $servicesRepository = $manager->getRepository(Services::class);
        
        $services = $servicesRepository->findAll();
        $users = $userRepository->loadUsersRoles();

        if (count($users) === 0 || count($services) === 0) {
            throw new \Exception("Il faut d'abord charger les fixtures pour User et Services.");
        }

        for ($i = 0; $i < 10; $i++) {
            $event = new Events();
            $event->setTitle($faker->sentence());
            $event->setDescription($faker->paragraph());
            $event->setLocation($faker->city());
            $startDate = $faker->dateTimeBetween('now', '+1 month');
            $event->setStart($startDate);
            $endDate = $faker->dateTimeBetween($startDate, $startDate->modify('+2 hours'));
            $event->setEnd($endDate);
            $event->setGoogleCalendarEventId([$faker->uuid()]);
            $event->setUser($faker->randomElement($users));
            $event->setServices($faker->randomElement($services));

            $manager->persist($event);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ServicesFixtures::class, 
        ];
    }
}
