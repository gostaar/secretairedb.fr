<?php

namespace App\Form;

use App\Entity\DocumentsUtilisateur;
use App\Entity\Dossier;
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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DocumentsUtilisateurType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userId = $options['userId'];
        $user = $this->entityManager->find(User::class, $userId);
        $service  = $options['service'];

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

        $typeDocuments = $this->getTypeDocumentsByService($service);

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
                'attr' => [
                    'class' => 'border-0',
                    
                ]
            ])
            ->add('destinataire', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Adresse du destinataire"
                ],
                "required" => false,
            ])
            ->add('objet', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Objet"
                ]
            ])
            // ->add('isActive', CheckboxType::class, [
            //     'label' => 'Actif',
            //     'required' => true,
            //     'data' => false,
            //     'mapped' => true,
            // ])
            ->add('details', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'style' => 'min-height: 300px;'
                ]
            ])
            ->add('dossier', EntityType::class, [
                'class' => Dossier::class,
                'choices' => $dossiers,
                'label' => false,
                'data' => $dossier,
            ])
            ->add('typeDocument', ChoiceType::class, [
                'label' => false,
                'choices' => array_combine($typeDocuments, $typeDocuments),
            ])
            ->add('images', CollectionType::class, [
                'label' => false,
                'entry_type' => AddImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' => [
                    'label' => false,
                    'service' => $service->getName()
                ],
                'attr' => [
                    'data-controller' => $service->getName() === "Telephonique" ? 'appel-collection' : 'image-collection',
                ],
            ])
            // ->add(
            //     $builder->create('images', FormType::class, ['by_reference' => true])
            //         ->add('slug', null, ['label' => false])
            //         ->add('imageDescription', null, ['label' => false])
            // )
            ->add('submit', SubmitType::class, [
                'label' => '<i class="fas fa-save d-inline d-md-none"></i><span class="d-none d-md-inline"> Enregistrer</span>',
                'label_html' => true,
            ])
        ;
    }

    private function getTypeDocumentsByService(?string $service): array
    {
        $types = [
            'Administratif' => ['Courrier', 'Email', 'Rapport', 'Pièce comptable'],
            'Commercial' => ['Rapport'],
            'Numerique' => ['Rapport'],
            'Telephonique' => ['Appel'],
        ];

        return $types[$service] ?? ['Autre'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DocumentsUtilisateur::class
        ]);
        $resolver->setDefined([
            'userId',
            'documentId',
            'dossierId',
            'service',
        ]);

    }
}
