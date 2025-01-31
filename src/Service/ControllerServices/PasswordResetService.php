<?php

namespace App\Service\ControllerServices;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Uid\Uuid;

class PasswordResetService
{
    private $entityManager;
    private $mailer;
    private $router;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer, UrlGeneratorInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function sendResetLink($user)
    {
        $resetToken = Uuid::v4()->toRfc4122();  // Génération d'un token unique
        $user->setPasswordResetToken($resetToken);
        $user->setPasswordResetExpiresAt(new \DateTime('+10 minutes'));  // Expiration dans 10 minutes
        $this->entityManager->flush();

        // Générer l'URL du lien de réinitialisation
        $resetUrl = $this->router->generate('app_reset_password', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL);

        // Créer et envoyer l'e-mail
        $email = (new Email())
            ->from('dbsecretaire@gmail.com')
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->html('<p>Bonjour,</p><p>Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant: <a href="' . $resetUrl . '">Réinitialiser mon mot de passe</a></p><p>Ce lien est valable 10 minutes.</p>');

        $this->mailer->send($email);
    }
}
