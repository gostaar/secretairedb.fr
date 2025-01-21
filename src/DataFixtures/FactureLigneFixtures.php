<?php
namespace App\DataFixtures;

use App\Entity\Facture;
use App\Entity\FactureLigne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FactureLigneFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $facturesRepository = $manager->getRepository(Facture::class);
        $factures = $facturesRepository->findAll();

        if (empty($factures)) {
            throw new \Exception('Aucune facture disponible pour créer des lignes.');
        }

        for ($i = 0; $i < 10; $i++) {
            $factureLigne = new FactureLigne();
            $factureLigne->setQuantite($faker->numberBetween(1, 100))
                ->setPrixUnitaire($faker->randomFloat(2, 100, 1000)) 
                ->setDesignation($faker->sentence) 
                ->setFacture($faker->randomElement($factures)); 

            $manager->persist($factureLigne);
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
