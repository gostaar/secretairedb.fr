<?php

namespace App\Controller\Admin;

use App\Entity\Devis;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class DevisCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Devis::class;
    }
        
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            NumberField::new('montant'),

            DateTimeField::new('date_devis'),

            ChoiceField::new('status'),

            TextField::new('commentaire'),
            BooleanField::new('is_active', 'Actif'),
            
            FormField::addFieldSet('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            AssociationField::new('client', 'Utilisateur associé')
                ->setFormTypeOptions([
                    'by_reference' => true, // Par défaut, mais explicite
                ])
                ->autocomplete(),
            AssociationField::new('devisLignes')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->autocomplete(),


        ];
        
    }
    
}
