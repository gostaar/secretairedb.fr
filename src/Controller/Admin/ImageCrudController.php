<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class ImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Image::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('id'),

            TextField::new('slug')
                ->onlyOnForms(),
            TextField::new('imageName')
                ->onlyOnForms(),
            TextField::new('imageSize')
                ->onlyOnForms(),
            TextField::new('imageDescription')
                ->onlyOnForms(),
            TextField::new('objet')
                ->onlyOnForms(),
            TextField::new('actions')
                ->onlyOnForms(),
                
            FormField::addFieldSet('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            AssociationField::new('document', 'Document associé')
                ->setFormTypeOptions([
                    'by_reference' => true, // Par défaut, mais explicite
                ])
                ->autocomplete(),
        ];
    }
    
}
