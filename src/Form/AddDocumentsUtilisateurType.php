<?php

namespace App\Form;

use App\Entity\DocumentsUtilisateur;
use App\Entity\Dossier;
use App\Entity\User;
use App\Form\AddImageType;
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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AddDocumentsUtilisateurType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $userId = $options['userId'];
        $dossierId  = $options['dossierId'];
        $service  = $options['service'];

        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $dossier = $this->entityManager->getRepository(Dossier::class)->find($dossierId);

        $typeDocuments = $this->getTypeDocumentsByService($service);

        $builder
            ->add('name', null, [
                'label' => 'Nom du document'
            ])
            ->add('typeDocument', ChoiceType::class, [
                'label' => 'Type de document',
                'choices' => array_combine($typeDocuments, $typeDocuments),
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'data' => $user,
                'label' => false,
                'attr' => ['style' => 'display: none;'],
            ])
            ->add('dossier', EntityType::class, [
                'class' => Dossier::class,
                'data' => $dossier,
                'label' => false,
                'attr' => ['style' => 'display: none;'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
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
            'data_class' => DocumentsUtilisateur::class,
        ]);
        $resolver->setDefined([
            'userId',
            'documentId',
            'dossierId',
            'service',
        ]);
    }
}
