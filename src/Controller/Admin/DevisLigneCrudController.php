<?php

namespace App\Controller\Admin;

use App\Entity\DevisLigne;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;


class DevisLigneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DevisLigne::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('designation'),
            NumberField::new('quanite'),
            NumberField::new('prix_unitaire'),
                       
            FormField::addPanel('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            AssociationField::new('devis')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->autocomplete(),
            
        ];
    }
    
}