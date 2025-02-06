<?php

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class JobController extends AbstractController
{
    // Route pour gérer l'envoi du formulaire de candidature
    #[Route('/jobs/send', name: 'jobs_handler', methods: ['POST'])]
    public function handleContactForm(Request $request, MailerInterface $mailer): Response
    {
        // Récupérer les données du formulaire
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $job = $request->request->get('job');
        $cv = $request->files->get('cv');

        if($job === "Autre"){
            $job = $request->request->get('customJob');
        }

        // Création d'un email pour la candidature
        $emailMessage = (new Email())
            ->from('contact@dbsecretaire.fr')
            ->to('dbsecretaire@gmail.com') // Adresse où tu souhaites recevoir la candidature
            ->subject('Nouvelle candidature pour le poste de ' . $job)
            ->html('<p>Nom : ' . $name . '</p>
                    <p>Email : ' . $email . '</p>
                    <p>Poste souhaité : ' . $job . '</p>
                    <p><strong>CV joint :</strong> ' . $cv->getClientOriginalName() . '</p>');

        // Ajouter le CV en pièce jointe
        $emailMessage->attachFromPath($cv->getPathname(), $cv->getClientOriginalName());

        // Envoi de l'email
        try {
            $mailer->send($emailMessage);
            $this->addFlash('success', 'Votre candidature a été envoyée avec succès !');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de votre candidature. Veuillez réessayer plus tard.');
        }

        // Redirection vers la page d'affichage des jobs
        return $this->redirect($this->generateUrl('subfragment', ['fragment' => 'part', 'subfragment' => 'job']));
    }
}
