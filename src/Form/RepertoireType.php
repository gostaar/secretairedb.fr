<?php

namespace App\Form;

use App\Entity\Dossier;
use App\Entity\Repertoire;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RepertoireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
            ->add('adresse', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
            ->add('codePostal', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
            ->add('ville', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
            ->add('pays', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
            ->add('telephone', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
            ->add('mobile', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
            ->add('email', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
            ->add('siret', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
            ->add('nomEntreprise', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
            ->add('contacts', CollectionType::class, [
                'label' => false,
                'entry_type' => \App\Form\AddContactType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'attr' => [
                    'data-controller' => 'contact-collection',
                ]
            ])       
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Repertoire::class,
        ]);
        $resolver->setDefined([
            'userId', 
            'dossierId'
        ]);
    }
}