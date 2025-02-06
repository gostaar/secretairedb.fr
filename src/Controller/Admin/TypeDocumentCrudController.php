<?php

namespace App\Controller\Admin;

use App\Entity\TypeDocument;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class TypeDocumentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TypeDocument::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            
            FormField::addFieldSet('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            AssociationField::new('documents')
                ->setFormTypeOptions([
                    'by_reference' => false, 
                ]),
        ];
    }
}
