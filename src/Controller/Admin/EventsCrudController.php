<?php

namespace App\Controller\Admin;

use App\Entity\Events;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;


class EventsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Events::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextField::new('description'),
            TextField::new('location'),
            DateTimeField::new('start'),
            DateTimeField::new('end'),
            ArrayField::new('google_calendar_event_id'),            
            
            FormField::addPanel('Relations avec d\'autres entités')->setIcon('fa fa-link'),
            AssociationField::new('services', 'Service associé')
                ->setFormTypeOptions([
                    'by_reference' => true, // Par défaut, mais explicite
                ])
                ->autocomplete(),
            AssociationField::new('user', 'Utilisateur associé')
                ->setFormTypeOptions([
                    'by_reference' => true, // Par défaut, mais explicite
                ])
                ->autocomplete(),
        ];
    }

}
