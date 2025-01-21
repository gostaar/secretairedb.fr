<?php

namespace App\DataFixtures;

use App\Entity\Devis;
use App\Entity\DevisVersion;
use App\Enum\DevisStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class DevisVersionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $devisRepository = $manager->getRepository(Devis::class);
        $devisList = $devisRepository->findAll();

        if (count($devisList) === 0) {
            throw new \Exception("Aucun devis trouvé. Veuillez charger les fixtures Devis d'abord.");
        }

        foreach ($devisList as $devis) {
            for ($i = 1; $i <= 3; $i++) {
                $devisVersion = new DevisVersion();
                $devisVersion->setMontant($devis->getMontant() + rand(-50, 50));
                $devisVersion->setCommentaire("Version $i pour le devis #" . $devis->getId());
                $devisVersion->setActive($i === 3);
                $devisVersion->setStatus(DevisStatus::from($faker->randomElement(DevisStatus::getValues())));                
                $devisVersion->setVersion("v$i");
                $devisVersion->setDateModification((new \DateTime())->modify("-" . (3 - $i) . " days"));

                $manager->persist($devisVersion);
            }
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
