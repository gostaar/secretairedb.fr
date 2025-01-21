<?php 
namespace App\DataFixtures;

use App\Entity\Facture;
use App\Entity\User;
use App\Enum\FactureStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FactureFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $userRepository = $manager->getRepository(User::class);
        $users = $userRepository->loadUsersRoles();

        if (empty($users)) {
            throw new \RuntimeException("Il n'y a pas d'utilisateurs dans la base de données pour créer des factures.");
        }

        for ($i = 0; $i < 10; $i++) {
            $facture = new Facture();
            $facture->setMontant($faker->randomFloat(2, 100, 1000)) 
                ->setDatePaiement($faker->dateTimeThisYear)           
                ->setDateFacture(new \DateTimeImmutable())           
                ->setStatus(FactureStatus::from($faker->randomElement(FactureStatus::getValues())))  
                ->setClient($faker->randomElement($users)) 
                ->setCommentaire($faker->sentence)                
                ->setActive($faker->boolean);                

            $manager->persist($facture);
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
