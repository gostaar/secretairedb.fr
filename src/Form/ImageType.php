<?php

namespace App\Form;

use App\Entity\DocumentsUtilisateur;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, [
                'mapped' => false, 
            ])
            ->add('slug', null, [
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('imageDescription', TextType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => 'Fichier',
            ])
            ->add('document', EntityType::class, [
                'class' => DocumentsUtilisateur::class,
                'choice_label' => 'name',
                'label' => false,
                'attr' => [
                    'class' => 'd-none', 
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
