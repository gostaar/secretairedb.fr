<?php

namespace App\Service;

class GoogleAuthService
{
    private $clientId;
    private $clientSecret;
    private $googleOauthScope;
    private $redirectUri;

    public function __construct(string $clientId, string $clientSecret, string $googleOauthScope, string $redirectUri)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->googleOauthScope = $googleOauthScope;
        $this->redirectUri = $redirectUri;
    }

    public function getGoogleOauthUrl(): string
    {
        return 'https://accounts.google.com/o/oauth2/auth?scope=' .
            urlencode($this->googleOauthScope) .
            '&redirect_uri=' . $this->redirectUri .
            '&response_type=code&client_id=' . $this->clientId .
            '&access_type=online';
    }
}
