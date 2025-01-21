<?php

namespace App\DataFixtures;

use App\Entity\Paiement;
use App\Entity\Facture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PaiementFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $factures = $manager->getRepository(Facture::class)->findAll();

        if (empty($factures)) {
            throw new \RuntimeException("Il n'y a pas de factures dans la base de données pour ajouter des paiements.");
        }

        foreach ($factures as $facture) {
            $paiement = new Paiement();
            $paiement->setFacture($facture) 
                ->setMontantPaye($faker->randomFloat(2, 10, $facture->getMontant()))
                ->setDatePaiement($faker->dateTimeThisYear);

            $manager->persist($paiement);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FactureFixtures::class,
        ];
    }
}
