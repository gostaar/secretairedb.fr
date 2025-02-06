<?php

namespace App\Controller\Admin;

use App\Entity\FactureLigne;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;


class FactureLigneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FactureLigne::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('designation'),
            NumberField::new('quanite'),
            NumberField::new('prix_unitaire'),
                       
            FormField::addFieldSet('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            AssociationField::new('facture')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->autocomplete(),
            
        ];
    }
    
}
