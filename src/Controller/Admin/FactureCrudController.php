<?php

namespace App\Controller\Admin;

use App\Entity\Facture;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;


class FactureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Facture::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            NumberField::new('montant'),
            DateTimeField::new('date_paiement'),
            DateTimeField::new('date_facture'),
            ChoiceField::new('status'),

            TextField::new('commentaire'),
            BooleanField::new('is_active', 'Actif')->setFormTypeOption('mapped', false),
            
            FormField::addFieldSet('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            AssociationField::new('paiements')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->autocomplete(),
            AssociationField::new('factureLignes')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->autocomplete(),
            
            AssociationField::new('client', 'Utilisateur associé')
                ->setFormTypeOptions([
                    'by_reference' => true, 
                ])
                ->autocomplete(),
        ];
    }
    
}
