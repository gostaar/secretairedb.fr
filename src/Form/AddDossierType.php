<?php

namespace App\Form;

use App\Entity\Dossier;
use App\Entity\Services;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class AddDossierType extends AbstractType
{
    
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userId = $options['userId'];
        $serviceId = $options['serviceId'];

        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $service =  $this->entityManager->getRepository(Services::class)->find($serviceId);

        $builder
            ->add('name')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'data' => $user,
                'label' => false,
                'attr' => ['style' => 'display: none;'],
            ])
            ->add('services', EntityType::class, [
                'class' => Services::class,
                'data' => $service,
                'label' => false,
                'attr' => ['style' => 'display: none;'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dossier::class,
        ]);

        $resolver->setDefined([
            'userId', 
            'serviceId'
        ]);
    }
}
