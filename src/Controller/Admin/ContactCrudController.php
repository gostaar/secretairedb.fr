<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom'),

            TextField::new('telephone')
                ->onlyOnForms(),
            TextField::new('email')
                ->onlyOnForms(),
            TextField::new('role')
                ->onlyOnForms(),
            TextField::new('commentaire')
                ->onlyOnForms(),
                
            FormField::addPanel('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            AssociationField::new('repertoire', 'Répertoire associé')
                ->setFormTypeOptions([
                    'by_reference' => true, // Par défaut, mais explicite
                ])
                ->autocomplete(),
        ];
    }
    
}
