<?php

namespace App\Form;

use App\Entity\Events;
use App\Entity\Services;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class EventsType extends AbstractType
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $agendaService = $this->entityManager->getRepository(Services::class)->findOneBy(['name' => 'Agenda']);

        $builder
            ->add('title', null, [
                'label' => "Titre",
            ])
            ->add('description', null, [
                'label' => "Description",
            ])
            ->add('location', null, [
                'label' => "Lieu",
            ])
            ->add('start', null, [
                'label' => "Date de début",
                'widget' => 'single_text',
            ])
            ->add('end', null, [
                'label' => "Date de fin",
                'widget' => 'single_text',
            ])
            // ->add('google_calendar_event_id')
            // ->add('services', EntityType::class, [
            //     'class' => Services::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Events::class,
        ]);
    }
}
