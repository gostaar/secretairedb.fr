<?php
namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    // Route pour afficher le formulaire
    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('contact/contact.html.twig');
    }

    // Route pour gérer l'envoi du formulaire de contact
    #[Route('/contact/send', name: 'contact_handler', methods: ['POST'])]
    public function handleContactForm(Request $request): Response
    {
        // Traiter le formulaire ici (envoi d'email, enregistrement, etc.)

        // Exemple de redirection après soumission
        $this->addFlash('success', 'Votre message a été envoyé avec succès !');
        return $this->redirectToRoute('contact');
    }
}
