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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


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
        $documentId = $options['documentId'];
        $document = $documentId && is_numeric($documentId) ? $this->entityManager->find(\App\Entity\DocumentsUtilisateur::class, $documentId) : null;
        $dossier = $document ? $document->getDossier() : null;

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
            // ->add('isActive', CheckboxType::class, [
            //     'label' => 'Actif',
            //     'required' => true,
            //     'data' => false,
            //     'mapped' => true,
            // ])
            ->add('details', TextareaType::class, [
                'attr' => [
                    'rows' => 5, // Nombre de lignes
                ],
            ])
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
        $resolver->setDefined(['documentId']);
    }
}
