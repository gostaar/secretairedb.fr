<?php

namespace App\Controller\Admin;

use App\Entity\Services;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class ServicesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Services::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // Champ ID (il est rare de le rendre modifiable)
            IdField::new('id')->onlyOnIndex(), // Affiché uniquement sur la page d'index

            // Champ Name
            TextField::new('name', 'Nom du service'),

            // Champ pour sélectionner un service dans les options disponibles
            ChoiceField::new('name')
                ->setChoices(Services::getAvailableServices()) // Utilisation de la méthode pour récupérer les services disponibles
                ->onlyOnForms(), // Affiché uniquement lors de la création / modification

            // Relation avec "Dossier" (un service peut être lié à plusieurs dossiers)
            FormField::addPanel('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            AssociationField::new('dossiers')
                ->setFormTypeOptions([
                    'by_reference' => false, // Permet de gérer la relation dans le formulaire
                ]),

            // Relation avec "Events" (un service peut être lié à plusieurs événements)
            AssociationField::new('events')
                ->setFormTypeOptions([
                    'by_reference' => false, // Permet de gérer la relation dans le formulaire
                ]),

            AssociationField::new('users')
                ->setFormTypeOptions(['by_reference' => false]),
        ];
    }
}
