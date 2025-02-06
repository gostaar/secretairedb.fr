<?php

namespace App\Service\ControllerServices;

use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use App\Entity\GoogleToken;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class GmailOAuthService
{
    private $entityManager;
    private $router;
    private $googleClient;
    private $accessToken;
    private $refreshToken;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $router, string $googleCredentialsPath)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;

        // Créer un client Google OAuth2
        $this->googleClient = new Google_Client();
        $this->googleClient->setAuthConfig($googleCredentialsPath);
        $this->googleClient->setScopes(['https://www.googleapis.com/auth/calendar']);
        $this->googleClient->setAccessType('offline');
        $this->googleClient->setPrompt('select_account consent');;

        // Vérifier si nous avons déjà un token d'accès valide ou si nous devons en obtenir un nouveau
        $this->loadTokens();
    }

    public function authenticate()
    {
        // Vérifie si un token d'accès est disponible, sinon redirige pour l'authentification
        if (null === $this->accessToken || $this->googleClient->isAccessTokenExpired()) {
            // Créer l'URL d'authentification OAuth2
            $authUrl = $this->googleClient->createAuthUrl();
            
            header('Location: ' . $authUrl);
            exit;
        }

        return null; // Si déjà authentifié, pas besoin de redirection
    }

    public function fetchAccessTokenWithAuthCode($authCode, $userId)
    {
        // Récupérer les tokens
        $accessToken = $this->googleClient->fetchAccessTokenWithAuthCode($authCode);

        // Vérifier que nous avons reçu un access token et un refresh token
        if (isset($accessToken['access_token']) && isset($accessToken['refresh_token'])) {
            $this->accessToken = $accessToken['access_token'];
            $this->refreshToken = $accessToken['refresh_token'];
            // Sauvegarder les tokens dans la base de données
            $this->saveTokens($userId);
        } else {
            throw new \Exception("Failed to retrieve access token or refresh token.");
        }
    }

    private function loadTokens()
    {
        $googleToken = $this->entityManager->getRepository(GoogleToken::class)->findOneBy([]);

        if ($googleToken) {
            $this->accessToken = $googleToken->getAccessToken();
            $this->refreshToken = $googleToken->getRefreshToken();
        }
    }

    private function saveTokens($userId)
    {
        $user = $this->entityManager->getRepository(\App\Entity\User::class)->find($userId);
        
        $existingTokens = $this->entityManager->getRepository(GoogleToken::class)->findBy(['users' => $user]);
        foreach ($existingTokens as $token) {
            $this->entityManager->remove($token);
        }
        $this->entityManager->flush();

        $googleToken = new GoogleToken();
        $googleToken->setUsers($user);
        $googleToken->setAccessToken($this->accessToken);
        $googleToken->setRefreshToken($this->refreshToken);
        $googleToken->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($googleToken);
        $this->entityManager->flush();
    }


}
