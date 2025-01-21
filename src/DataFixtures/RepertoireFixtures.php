<?php 
namespace App\DataFixtures;

use App\Entity\Repertoire;
use App\Entity\User;
use App\Entity\Dossier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RepertoireFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $userRepository = $manager->getRepository(User::class);
        $dossiers = $manager->getRepository(Dossier::class)->findAll();
        $userRepository = $manager->getRepository(User::class);
        $users = $userRepository->loadUsersRoles();

        if (empty($users)) {
            throw new \RuntimeException("Il n'y a pas d'utilisateurs dans la base de données.");
        }
        
        if (empty($dossiers)) {
            throw new \RuntimeException("Il n'y a pas de dossiers dans la base de données.");
        }

        foreach ($users as $user) {
            foreach ($dossiers as $dossier) {
                $repertoire = new Repertoire();
                $repertoire->setNom($faker->company)
                    ->setAdresse($faker->address)
                    ->setCodePostal($faker->postcode)
                    ->setVille($faker->city)
                    ->setPays($faker->country)
                    ->setTelephone($faker->phoneNumber)
                    ->setMobile($faker->phoneNumber)
                    ->setEmail($faker->email)
                    ->setSiret($faker->numerify('######### #####'))
                    ->setNomEntreprise($faker->company)
                    ->setUser($faker->randomElement($users)) 
                    ->setDossier($dossier); 

                $manager->persist($repertoire);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DossierFixtures::class,
        ];
    }
}
