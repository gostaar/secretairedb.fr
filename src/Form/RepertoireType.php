<?php

namespace App\Form;

use App\Entity\Dossier;
use App\Entity\Repertoire;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RepertoireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('adresse')
            ->add('codePostal')
            ->add('ville')
            ->add('pays')
            ->add('telephone')
            ->add('mobile')
            ->add('email')
            ->add('siret')
            ->add('nomEntreprise')
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('dossier', EntityType::class, [
            //     'class' => Dossier::class,
            //     'choice_label' => 'id',
            // ]) 
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer le Répertoire',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Repertoire::class,
        ]);
    }
}
