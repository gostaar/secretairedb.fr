<?php
namespace App\Controller\Google;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ControllerServices\GmailOAuthService;
use App\Controller\User\UserController;
use App\Service\ControllerServices\FragmentDataService;

class CalendarController extends AbstractController
{
    private $entityManager;
    private $gmailOAuthService;
    private $userController;
    private $fragmentDataService;

    public function __construct(
        EntityManagerInterface $entityManager, 
        GmailOAuthService $gmailOAuthService,
        UserController $userController,
        FragmentDataService $fragmentDataService,
    )
    {
        $this->entityManager = $entityManager;
        $this->gmailOAuthService = $gmailOAuthService;
        $this->userController = $userController;
        $this->fragmentDataService = $fragmentDataService;
    }

    #[Route('/gmail_auth', name: 'gmail_auth')]
    public function authenticate(Request $request)
    {
        $service = 'Agenda';
        $formData = $this->userController->main(null, null, null, $service);
        
        $googleToken = $this->entityManager->getRepository(\App\Entity\GoogleToken::class)->findBy(['users' => $this->getUser()->getId()]);

        if ($googleToken && $this->isTokenValid($googleToken)) {
        
            $events = $this->fragmentDataService->fetchGoogleCalendarEvents($googleToken[0]->getAccessToken(), $this->getUser()->getId());
            
            return $this->render('partials/user/Agenda/_agendaMain.html.twig', [
                'events' => $events ?  $events : $formData['events'],
                'services' => $formData['services'],
                'service' => $service,
                'origin' => 'Google',
            ]);
        } 

        return $this->redirect($this->gmailOAuthService->authenticate());
            
    }

    #[Route('/oauth2callback', name: 'oauth2callback')]
    public function oauth2Callback(Request $request) : Response
    {
        $authCode = $request->get('code');
        
        if (!$authCode) {
            return $this->render('partials/user/Agenda/_agenda.html.twig');
        }
        
        $this->gmailOAuthService->fetchAccessTokenWithAuthCode($authCode, $this->getUser()->getId());
            
        return $this->redirectToRoute('event', [
            'service' => 'Agenda',
            'origin' => 'Google'
        ]);
    }

    #[Route('/google_disconnect', name: 'google_disconnect')]
    public function googleDisconnect()
    {
       $existingTokens = $this->entityManager->getRepository(\App\Entity\GoogleToken::class)->findBy(['users' => $this->getUser()->getId()]);
        foreach ($existingTokens as $token) {
            $this->entityManager->remove($token);
        }

        $this->entityManager->flush();
        return $this->render('partials/user/Agenda/_agenda.html.twig');
    }

    private function isTokenValid($googleToken): bool
    {
        $expirationTime = $googleToken[0]->getCreatedAt()->getTimestamp() + 3600;
        $currentTime = (new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')))->getTimestamp();
        return $expirationTime > $currentTime;
    }
}