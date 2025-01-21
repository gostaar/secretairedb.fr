<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\DevisLigne;
use App\Entity\User;
use App\Enum\DevisStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant')
            ->add('date_devis', null, [
                'widget' => 'single_text',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => DevisStatus::cases(),
                'choice_label' => fn (DevisStatus $choice) => $choice->getLabel(),
                'data' => DevisStatus::EN_ATTENTE->value,
            ])
            ->add('commentaire')
            ->add('is_active', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
            ])
            // ->add('client', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer le devis',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
