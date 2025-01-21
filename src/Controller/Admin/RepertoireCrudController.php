<?php

namespace App\Controller\Admin;

use App\Entity\Repertoire;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormPanel;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormRow;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class RepertoireCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Repertoire::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addPanel('Informations Générales')->setIcon('fa fa-info-circle'),
            FormField::addRow(),
            TextField::new('nom', 'Nom du Répertoire')
                ->setColumns(6),

            FormField::addPanel('Adresse')->setIcon('fa fa-map-marker-alt'),
            FormField::addRow(),
            TextField::new('adresse', 'Adresse')
                ->onlyOnForms()
                ->setColumns(6),
            TextField::new('code_postal', 'Code Postal')
                ->onlyOnForms()
                ->setColumns(6),
            FormField::addRow(),
            TextField::new('ville', 'Ville')
                ->onlyOnForms()
                ->setColumns(6),
            TextField::new('pays', 'Pays')
                ->onlyOnForms()
                ->setColumns(6),

            FormField::addPanel('Contact')->setIcon('fa fa-phone'),
            FormField::addRow(),
            TextField::new('telephone', 'Téléphone')
                ->onlyOnForms()
                ->setColumns(4),
            TextField::new('mobile', 'Mobile')
                ->onlyOnForms()
                ->setColumns(4),
            TextField::new('email', 'Email')
                ->onlyOnForms()
                ->setColumns(4),

            FormField::addPanel('Entreprise')->setIcon('fa fa-building'),
            FormField::addRow(),
            TextField::new('siret', 'SIRET')
                ->onlyOnForms()
                ->setColumns(6),
            TextField::new('nom_entreprise', 'Nom de l\'Entreprise')
                ->onlyOnForms()
                ->setColumns(6),

            FormField::addPanel('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            FormField::addRow(),
            AssociationField::new('user', 'Utilisateur associé')
                ->setFormTypeOptions(['by_reference' => true])
                ->autocomplete()
                ->setColumns(4),
            AssociationField::new('dossier', 'Dossier associé')
                ->setFormTypeOptions(['by_reference' => true])
                ->autocomplete()
                ->setColumns(4),
            AssociationField::new('contacts', 'Contact associé')
                ->setFormTypeOptions(['by_reference' => false])
                ->autocomplete()
                ->setColumns(4),
        ];
    }
}
