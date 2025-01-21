<?php

namespace App\DataFixtures;

use App\Entity\Devis;
use App\Entity\User;
use App\Enum\DevisStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory as FakerFactory;

class DevisFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $userRepository = $manager->getRepository(User::class);
        $client = $userRepository->loadUsersRoles();

        if (!$client) {
            throw new \Exception("Client 'user@example.com' introuvable. Veuillez vous assurer que les fixtures User sont chargées.");
        }

        $devisData = [
            [
                'montant' => 1500.50,
                'date_devis' => new \DateTimeImmutable('2024-12-01'),
                'status' => DevisStatus::ACCEPTE,
                'commentaire' => 'Premier devis accepté pour un projet de rénovation.',
                'is_active' => true,
            ],
            [
                'montant' => 2500.75,
                'date_devis' => new \DateTimeImmutable('2024-12-05'),
                'status' => DevisStatus::EN_ATTENTE,
                'commentaire' => 'En attente de validation pour un projet de construction.',
                'is_active' => true,
            ],
            [
                'montant' => 500.00,
                'date_devis' => new \DateTimeImmutable('2024-11-28'),
                'status' => DevisStatus::REFUSE,
                'commentaire' => 'Devis refusé en raison d’un budget trop élevé.',
                'is_active' => false,
            ],
        ];

        foreach ($devisData as $data) {
            $devis = new Devis();
            $devis->setMontant($data['montant']);
            $devis->setDateDevis($data['date_devis']);
            $devis->setStatus($data['status']);
            $devis->setCommentaire($data['commentaire']);
            $devis->setIsActive($data['is_active']);
            $devis->setClient($faker->randomElement($client));

            $manager->persist($devis);
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
