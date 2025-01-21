<?php
namespace App\DataFixtures;

use App\Entity\Devis;
use App\Entity\DevisLigne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class DevisLigneFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $devis = $manager->getRepository(Devis::class)->findAll();

        if (empty($devis)) {
            throw new \Exception('Aucune Devis disponible pour créer des lignes.');
        }

        for ($i = 0; $i < 10; $i++) {
            $DevisLigne = new DevisLigne();
            $DevisLigne->setQuantite($faker->numberBetween(1, 100))
                ->setPrixUnitaire($faker->randomFloat(2, 100, 1000)) 
                ->setDesignation($faker->sentence) 
                ->setDevis($faker->randomElement($devis)); 

            $manager->persist($DevisLigne);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DevisFixtures::class,
        ];
    }
}
