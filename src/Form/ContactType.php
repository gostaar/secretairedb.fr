<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Repertoire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('telephone')
            ->add('email')
            ->add('role')
            ->add('commentaire')
            // ->add('repertoire', EntityType::class, [
            //     'class' => Repertoire::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('save', SubmitType::class, [
            //     'label' => 'Enregistrer le contact',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
