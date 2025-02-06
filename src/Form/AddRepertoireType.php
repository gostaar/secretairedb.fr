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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\ORM\EntityManagerInterface;

class AddRepertoireType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userId = $options['userId'];
        $dossierId = $options['dossierId'];
        
        $user = $this->entityManager->getRepository(\App\Entity\User::class)->find($userId);
        $dossier = $this->entityManager->getRepository(\App\Entity\Dossier::class)->find($dossierId);

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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Repertoire::class,
        ]);
        $resolver->setDefined([
            'userId', 
            'dossierId'
        ]);
    }
}
