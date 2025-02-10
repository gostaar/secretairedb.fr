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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
          // Vérifie le contenu des options ici
        $service = $options['service'];

        $builder
            ->add('slug', null, ['label' => false])
            ->add('imageDescription', null, ['label' => false])
            ->add('objet', null, [
                'label' => false,
                'attr' => [
                    'class' => $service === "Telephonique" ? "d-flex" : "d-none",
                     "style" => "height:38px;"
                ]
            ])
            ->add('actions', null, [
                'label' => false,
                'attr' => [
                    'class' => $service === "Telephonique" ? "d-flex" : "d-none"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'service' => null
        ]);
        $resolver->setDefined([
            'service',
        ]);
    }
}
