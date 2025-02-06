<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\DocumentsUtilisateur;
use App\Entity\Image;
use Faker\Factory as FakerFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// use Vich\UploaderBundle\Entity\File as VichFile;
use Symfony\Component\HttpFoundation\File\File;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $documents = $manager->getRepository(DocumentsUtilisateur::class)->findAll();

        $imagePath = 'public/uploads/documents/677ea9a7b478b.png';
        
        for ($i = 0; $i < 200; $i++) {
            $image = new Image();

            if (file_exists($imagePath)) {
                $imageFile = new File($imagePath);
                $image->setImageName($imageFile->getFileName());
            } else {
                // Gérer l'absence de fichier ou utiliser un fichier factice
                $image->setImageFile(new File('public/uploads/documents/default.png'));
                $image->setImageName('default.png');
            }
            // $image->setFilePath('/uploads/documents/' . $image->getImageName());
            $image->setSlug($faker->word());
            $image->setDocument($faker->randomElement($documents));

            $manager->persist($image);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DocumentsUtilisateurFixtures::class,
        ];
    }
}
