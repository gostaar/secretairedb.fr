<?php

namespace App\Controller\Admin;

use App\Entity\Dossier;
use App\Entity\Services;
use App\Entity\DocumentsUtilisateur;
use App\Entity\Repertoire;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class DossierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Dossier::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // Affichage de l'ID du dossier (uniquement en mode index)
            IdField::new('id')->onlyOnIndex(),

            // Champ pour le nom du dossier
            TextField::new('name', 'Nom du dossier'),

            FormField::addPanel('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            AssociationField::new('services', 'Service associé')
                ->setFormTypeOptions([
                    'by_reference' => true, // Par défaut, mais explicite
                ])
                ->autocomplete(),

            // Association avec les documents utilisateurs
            AssociationField::new('documents')
                ->setFormTypeOptions([
                    'by_reference' => false, // Permet d'ajouter ou de supprimer des documents
                ])
                ->autocomplete(),

            // Association avec les répertoires
            AssociationField::new('repertoires')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->autocomplete(),

            AssociationField::new('user', 'Utilisateur associé')
                ->setFormTypeOptions([
                    'by_reference' => true, // Par défaut, mais explicite
                ])
                ->autocomplete(),
        ];
    }
}
