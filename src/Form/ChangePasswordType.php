<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'constraints' => [
                    new NotBlank(),
                    new UserPassword(['message' => 'Mot de passe actuel incorrect.'])
                ],
                'mapped' => false, // Ce champ n'est pas lié à l'entité User
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 6, 'minMessage' => 'Le mot de passe doit comporter au moins 6 caractères.']),
                ],
                'mapped' => false,
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
                'constraints' => [new NotBlank()],
                'mapped' => false,
            ]);
    }
}
