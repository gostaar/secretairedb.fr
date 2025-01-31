<?php

namespace App\Form;

use App\Entity\DocumentsUtilisateur;
use App\Entity\Dossier;
use App\Entity\TypeDocument;
use App\Entity\User;
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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

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
        // dd($documentId, $document, $dossier);

        $builder
            ->add('id', null, [
                'label' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'd-none'
                ]
            ])
            ->add('date_document', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'label' => false,
                'attr' => ['class' => 'border-0']
            ]) 
            ->add('name', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
            ->add('expediteur', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
            ->add('destinataire', null, [
                'label' => false,
                'attr' => ['class' => 'border-0']
            ])
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
                'label' => false,
                'attr' => ['class' => 'border-0']
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
                'label' => false,
                'data' => $dossier,
            ])
            ->add('typeDocument', EntityType::class, [
                'class' => TypeDocument::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'required' => false,
                'label' => false,
                'mapped' => true,
            ])
            ->add('images', CollectionType::class, [
                'label' => false,
                'entry_type' => AddImageType::class,
                'allow_add' => true, // Permet d'ajouter de nouveaux éléments
                'allow_delete' => true, // Permet de supprimer des éléments
                'by_reference' => false, // Permet de travailler avec des objets sans les référencer directement
                'entry_options' => [
                    'label' => false,
                ],
                'attr' => [
                    'data-controller' => 'image-collection',
                ],
            ])
            // ->add(
            //     $builder->create('images', FormType::class, ['by_reference' => true])
            //         ->add('slug', null, ['label' => false])
            //         ->add('imageDescription', null, ['label' => false])
            // )
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
        
        $resolver->setDefined([
            'user', 
            'documentId'
        ]);

    }
}
