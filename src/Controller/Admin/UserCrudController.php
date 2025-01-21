<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // Bloc : Identifiant utilisateur
            FormField::addPanel('Identifiant utilisateur')->setIcon('fa fa-id-card'),
            IdField::new('id', 'ID')
                ->onlyOnIndex(),
            EmailField::new('email', 'Email'),

            ChoiceField::new('roles', 'Rôles')
                ->setChoices([
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                ])
                ->allowMultipleChoices()
                ->setRequired(true),

            // Bloc : Informations personnelles
            FormField::addPanel('Informations personnelles')->setIcon('fa fa-user'),

            // Nom et Entreprise côte à côte
            FormField::addRow(),
            TextField::new('nom', 'Nom complet')
                ->setColumns(6),
            TextField::new('nomEntreprise', 'Nom de l\'entreprise')
                ->onlyOnForms()
                ->setColumns(6),

            // Adresse, Code postal et Ville côte à côte
            FormField::addRow(),
            TextField::new('adresse', 'Adresse')
                ->onlyOnForms()
                ->setColumns(6),
            TextField::new('codePostal', 'Code Postal')
                ->onlyOnForms()
                ->setColumns(3),
            TextField::new('ville', 'Ville')
                ->onlyOnForms()
                ->setColumns(3),

            // Téléphone et Mobile côte à côte
            FormField::addRow(),
            TextField::new('telephone', 'Téléphone fixe')
                ->onlyOnForms()
                ->setColumns(6),
            TextField::new('mobile', 'Téléphone mobile')
                ->onlyOnForms()
                ->setColumns(6),

            // Bloc : Informations professionnelles
            FormField::addPanel('Informations professionnelles')->setIcon('fa fa-building'),
            TextField::new('siret', 'Numéro SIRET')
                ->onlyOnForms(),

            // Bloc : Relations avec d'autres entités
            FormField::addPanel('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            AssociationField::new('services', 'Services')
                ->setFormTypeOptions(['by_reference' => false])
                ->autocomplete(),
            AssociationField::new('factures', 'Factures')
                ->setFormTypeOptions(['by_reference' => false])
                ->autocomplete(),
            AssociationField::new('devis', 'Devis')
                ->setFormTypeOptions(['by_reference' => false])
                ->autocomplete(),
        ];
    }
}
