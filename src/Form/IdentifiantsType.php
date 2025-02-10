<?php

namespace App\Form;

use App\Entity\Identifiants;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class IdentifiantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, [
                'mapped' => false, 
            ])
            ->add('site', null, [
                'label' => "Site"
            ])
            ->add('identifiant', null, [
                'label' => "Identifiant"
            ])
            ->add('password', null, [
                'label' => 'Mot de passe'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Identifiants::class,
        ]);
    }
}
