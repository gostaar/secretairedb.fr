<?php

namespace App\Form;

use App\Entity\DocumentsUtilisateur;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageName', null, [
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('imageSize', null, [
                'label' => 'Taille',
                'required' => false,
            ])
            ->add('imageFile', VichImageType::class)
            // ->add('updatedAt', null, [
            //     'widget' => 'single_text',
            //     'label' => false,
            //     'attr' => ['style' => 'display: none;'],
            // ])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            //     'label' => false,
            //     'attr' => ['style' => 'display: none;'],
            // ])
            // ->add('document', EntityType::class, [
            //     'class' => DocumentsUtilisateur::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
