<?php

namespace App\Form;

use App\Entity\DocumentsUtilisateur;
use App\Entity\Dossier;
use App\Entity\TypeDocument;
use App\Entity\User;
use App\Form\ImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class DocumentsUtilisateurType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userId = $options['user']->getId();
        $user = $this->entityManager->find(User::class, $userId);
        $dossierId = $options['dossierId'];
        $dossier = $dossierId && is_numeric($dossierId) ? $this->entityManager->find(\App\Entity\Dossier::class, $dossierId) : null;

        $serviceId = 0;
        
        if ($dossier) {
            $service = $dossier->getServices();
            $serviceId = $service ? $service->getId() : null;
        }

        $dossiers = $this->entityManager->getRepository(\App\Entity\Dossier::class)->findBy([
            'services' => $serviceId,
            'user' => $userId,
        ]);

        $builder
            ->add('date_document', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
            ->add('name', null, [
                'label' => 'Nom',
            ])
            ->add('expediteur')
            ->add('destinataire')
            ->add('isActive', CheckboxType::class, [
                'label' => 'Actif',
                'required' => true,
                'data' => false,
                'mapped' => true,
            ])
            ->add('details')
            ->add('user', EntityType::class, [
                'class' => \App\Entity\User::class,
                'data' => $user,
                'label' => false,
                'attr' => ['style' => 'display: none;'],
            ])
            ->add('dossier', EntityType::class, [
                'class' => Dossier::class,
                'choices' => $dossiers,
                'data' => $dossier,
            ])
            ->add('typeDocument', EntityType::class, [
                'class' => TypeDocument::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'required' => false,
                'mapped' => true,
            ])
            ->add('images', CollectionType::class, [
                'label' => false,
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'mapped' => false,
                'entry_options' => [
                    'label' => false,
                    'attr' => ['class' => 'd-flex form-element-image']
                ],
                'attr' => [
                    'data-controller' => 'image-collection',
                ]
            ])       
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DocumentsUtilisateur::class,
        ]);
        $resolver->setDefined(['user']);
        $resolver->setDefined(['dossierId']);
    }
}
