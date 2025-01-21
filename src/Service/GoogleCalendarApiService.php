<?php

namespace App\Service;

use Google_Client;
use Google_Service_Calendar;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GoogleCalendarApiService
{
    private $client;
    private $service;

    public function __construct(string $clientId, string $clientSecret, string $redirectUri)
    {
        $this->client = new Google_Client();
        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
        $this->client->setRedirectUri($redirectUri);
        $this->client->setScopes([Google_Service_Calendar::CALENDAR]);
    }

    // Fonction pour récupérer le token d'accès
    public function getAccessToken(string $clientId, string $redirectUri, string $clientSecret, string $code, SessionInterface $session): array
    {
        $response = $this->client->request('POST', self::OAUTH2_TOKEN_URI, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'client_secret' => $clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
            ],
        ]);

        // Gestion de la réponse
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            throw new BadRequestException('Failed to receive access token');
        }

        $accessToken = $response->toArray();
        $session->set('AccessToken', $accessToken);
        return $response->toArray(); // Retourne le tableau des données JSON
    }

    // Fonction pour récupérer le fuseau horaire de l'utilisateur
    public function getUserCalendarTimeZone(string $accessToken): string
    {
        $response = $this->client->request('GET', self::CALENDAR_TIMEZONE_URI, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ]
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            throw new BadRequestException('Failed to fetch timezone');
        }

        $data = $response->toArray();
        return $data['value'];
    }

    // Fonction pour récupérer la liste des calendriers de l'utilisateur
    public function getCalendarList(string $accessToken): array
    {
        $urlParameters = [
            'fields' => 'items(id,summary,timeZone)',
            'minAccessRole' => 'owner',
        ];

        $response = $this->client->request('GET', self::CALENDAR_LIST, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
            'query' => $urlParameters, // Paramètres d'URL
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            throw new BadRequestException('Failed to get calendars list');
        }

        return $response->toArray()['items'];
    }

    // Fonction pour créer un événement dans un calendrier
    public function createCalendarEvent(
        string $accessToken, 
        array $eventData,  // eventData contenant 'google_calendar_event_id'
        bool $allDay, 
        array $eventDatetime, 
        string $eventTimezone
    ): ?string {
        // Extraire calendarId et eventId à partir de google_calendar_event_id
        $googleCalendarEventId = $eventData['google_calendar_event_id'] ?? ['primary', null];
        $calendarId = $googleCalendarEventId[0] ?? 'primary'; // Default to 'primary' if no calendarId is set
        $eventId = $googleCalendarEventId[1] ?? null;  // Default to null if no eventId is set
    
        if (empty($calendarId)) {
            throw new InvalidArgumentException("Calendar ID is required.");
        }
    
        // Utilisation de calendarId pour créer l'URL de l'API
        $apiURL = self::CALENDAR_EVENT . $calendarId . '/events';
    
        $curlPost = [];
    
        if (!empty($eventData['summary'])) {
            $curlPost['summary'] = $eventData['summary'];
        }
    
        if (!empty($eventData['location'])) {
            $curlPost['location'] = $eventData['location'];
        }
    
        if (!empty($eventData['description'])) {
            $curlPost['description'] = $eventData['description'];
        }
    
        // Gestion des dates et heures
        $eventDate = !empty($eventDatetime['event_date']) ? $eventDatetime['event_date'] : date("Y-m-d");
        $startTime = !empty($eventDatetime['start_time']) ? $eventDatetime['start_time'] : date("H:i:s");
        $endTime = !empty($eventDatetime['end_time']) ? $eventDatetime['end_time'] : date("H:i:s");
    
        if ($allDay) {
            $curlPost['start'] = ['date' => $eventDate];
            $curlPost['end'] = ['date' => $eventDate];
        } else {
            $timezoneOffset = $this->getTimezoneOffset($eventTimezone);
            $timezoneOffset = !empty($timezoneOffset) ? $timezoneOffset : '+00:00';
    
            $dateTimeStart = $eventDate . 'T' . $startTime . $timezoneOffset;
            $dateTimeEnd = $eventDate . 'T' . $endTime . $timezoneOffset;
    
            $curlPost['start'] = [
                'dateTime' => $dateTimeStart,
                'timeZone' => $eventTimezone
            ];
            $curlPost['end'] = [
                'dateTime' => $dateTimeEnd,
                'timeZone' => $eventTimezone
            ];
        }
    
        // Ajouter eventId si nécessaire dans les données de la requête
        if ($eventId) {
            $curlPost['eventId'] = $eventId; // Si l'eventId doit être utilisé dans le corps de la requête
        }
    
        // Création de l'événement via une requête POST
        $response = $this->client->request('POST', $apiURL, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => $curlPost,
        ]);
    
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            throw new BadRequestException('Error during event creation');
        }
    
        $data = $response->toArray();
        return $data['id'] ?? null;
    }
    

    // Fonction pour obtenir le décalage horaire pour un fuseau donné
    public function getTimezoneOffset(string $timezone = 'Europe/Paris'): string
    {
        $current = new \DateTimeZone($timezone);
        $utcTime = new \DateTime('now', new \DateTimeZone('UTC'));
        $offsetInSecs = $current->getOffset($utcTime);
        $hoursAndSec = gmdate('H:i', abs($offsetInSecs));
        return $offsetInSecs < 0 ? "-{$hoursAndSec}" : "+{$hoursAndSec}";
    }

    public function refreshAccessToken(string $refreshToken): string
    {
        $this->client->setAccessToken(['refresh_token' => $refreshToken]);

        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
        }

        return $this->client->getAccessToken()['access_token'];
    }
}
