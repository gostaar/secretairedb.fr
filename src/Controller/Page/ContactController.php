<?php
namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use App\Service\EmailService;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ContactController extends AbstractController
{
    private $emailService;

    public function __construct( EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    // Route pour gérer l'envoi du formulaire de contact
    #[Route('/contact/send', name: 'contact_handler', methods: ['POST'])]
    public function sendEmail(Request $request): Response
    {
        $name = $request->request->get('subject');
        $email = $request->request->get('email');
        $message = $request->request->get('message');

        $this->emailService->sendContactEmail($name, $email, $message);

        $this->addFlash('success', 'Message envoyé avec succès !');

        return $this->redirect($this->generateUrl('subfragment', ['fragment' => 'part', 'subfragment' => 'contact']));
    }
}
