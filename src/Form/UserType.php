<?php

namespace App\Form;

use App\Entity\Services;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('password')
            ->add('lastActivity', null, [
                'widget' => 'single_text',
            ])
            ->add('nom')
            ->add('adresse')
            ->add('codePostal')
            ->add('ville')
            ->add('pays')
            ->add('telephone')
            ->add('mobile')
            ->add('siret')
            ->add('nomEntreprise')
            // ->add('services', EntityType::class, [
            //     'class' => Services::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
