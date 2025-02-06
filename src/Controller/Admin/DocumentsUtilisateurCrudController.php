<?php

namespace App\Controller\Admin;

use App\Entity\DocumentsUtilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class DocumentsUtilisateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DocumentsUtilisateur::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // Date du document, avec un champ DateTimeField
            DateTimeField::new('date_document', 'Date du document')
                ->onlyOnForms()
                ->setFormTypeOptions([
                    'widget' => 'single_text', // Format d'affichage des dates
                ]),

            // Nom du document
            TextField::new('name'),

            // Expéditeur
            TextField::new('expediteur')
                ->onlyOnForms(),

            // Destinataire
            TextField::new('destinataire')
                ->onlyOnForms(),

            // Chemin du fichier, avec un champ FileField pour gérer le téléchargement
            ImageField::new('file_path', 'Fichier')
                ->setFormTypeOptions([
                    'required' => false,
                ])
                ->onlyOnForms()
                ->setUploadDir('public/uploads/documents') // Répertoire de téléchargement
                ->setBasePath('uploads/documents'),

            // Champ booléen pour indiquer si le document est actif
            BooleanField::new('is_active', 'Actif')
                ->onlyOnForms(),

            // Détails du document
            TextField::new('details')
                ->onlyOnForms(),

            FormField::addFieldSet('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            AssociationField::new('user', 'Utilisateur associé')
                ->setFormTypeOptions([
                    'by_reference' => true,
                ])
                ->autocomplete(),

            // Association avec le dossier
            AssociationField::new('dossier', 'Dossier associé')
                ->setFormTypeOptions([
                    'by_reference' => true,
                ])
                ->autocomplete(),

            // Association avec le type de document
            AssociationField::new('typeDocument', 'Type de document associé')
                ->setFormTypeOptions([
                    'by_reference' => true,
                ])
                ->autocomplete(),
        ];
    }
}
