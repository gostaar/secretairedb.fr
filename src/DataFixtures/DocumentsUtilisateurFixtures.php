<?php

namespace App\DataFixtures;

use App\Entity\DocumentsUtilisateur;
use App\Entity\User;
use App\Entity\Dossier;
use App\Entity\TypeDocument;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class DocumentsUtilisateurFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $userRepository = $manager->getRepository(User::class);
        $users = $userRepository->loadUsersRoles();
        $dossierRepository = $manager->getRepository(Dossier::class);
        $typeDocumentRepository = $manager->getRepository(TypeDocument::class);
        
        $dossiers = $dossierRepository->findAll();
        $typeDocuments = $typeDocumentRepository->findAll();

        if (count($users) === 0 || count($dossiers) === 0 || count($typeDocuments) === 0) {
            throw new \Exception("Il faut d'abord charger les fixtures pour User, Dossier et TypeDocument.");
        }

        for ($i = 0; $i < 100; $i++) {
            $document = new DocumentsUtilisateur();
            $document->setDateDocument($faker->dateTimeBetween('-1 year', 'now'));
            $document->setName($faker->word() . '.pdf');
            $document->setExpediteur($faker->company);
            $document->setDestinataire($faker->name);
            // $document->setFilePath($faker->filePath());
            $document->setIsActive($faker->boolean());
            $document->setDetails($faker->text());

            $document->setUser($faker->randomElement($users));
            $document->setDossier($faker->randomElement($dossiers));
            $document->setTypeDocument($faker->randomElement($typeDocuments));

            $manager->persist($document);
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
