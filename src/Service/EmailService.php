<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class EmailService
{
    private $mailer;
    private $router;
    private $em;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $router, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->em = $em;
    }

    public function sendContactEmail(string $name, string $email, string $message)
    {
        $emailMessage = (new Email())
            ->from('contact@dbsecretaire.fr')  // Adresse d’envoi
            ->to('dbsecretaire@gmail.com')         // Adresse de réception
            ->subject('Nouveau message de contact')
            ->html("
                <p><strong>Nom :</strong> {$name}</p>
                <p><strong>Email :</strong> {$email}</p>
                <p><strong>Message :</strong><br>{$message}</p>
            ");

        $this->mailer->send($emailMessage);
    }
}
