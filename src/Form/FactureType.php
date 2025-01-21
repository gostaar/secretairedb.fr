<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\User;
use App\Form\FactureLigneType;
use App\Enum\FactureStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $facture = $options['data'];
        $builder
            ->add('montant')
            ->add('date_paiement', null, [
                'widget' => 'single_text',
            ])
            ->add('date_facture', null, [
                'widget' => 'single_text',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => FactureStatus::cases(),
                'choice_label' => fn (FactureStatus $choice) => $choice->getLabel(),
                'data' => FactureStatus::NON_PAYE->value,
            ])
            ->add('commentaire')
            ->add('is_active', CheckboxType::class, [
                'label' => 'Active',
                'required' => false,
                'mapped' => false,
                'data' => $facture->isActive(),
            ])
            // ->add('client', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
            ->add('factureLignes', CollectionType::class, [
                'label' => false,
                'entry_type' => FactureLigneType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' => ['label' => false],
                'attr' => [
                    'data-controller' => 'form-collection'
                ]
            ])        
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer la facture',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
