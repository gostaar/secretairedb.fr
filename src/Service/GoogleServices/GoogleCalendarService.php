<?php
namespace App\Service\GoogleServices;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class GoogleCalendarService
{
    private $httpClient;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function getUserInfo(string $accessToken): array
    {
        $response = $this->httpClient->request('GET', $_ENV['GOOGLE_URL_INFO'], [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
            ],
            'verify_peer' => true,
            'verify_host' => true,
            'cafile' => '/etc/ssl/certs/ca-certificates.crt', 
        ]);
        return $response->toArray();

    }

    public function listEvents(string $accessToken): array
    {
        $response = $this->httpClient->request('GET', $_ENV['GOOGLE_URL_EVENTS'], [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
            ],
        ]);

        return $response->toArray();
    }

    public function createEvent(string $accessToken, array $eventData): array
    {
        // Vérification de la validité des données
        if (empty($eventData['start']) || empty($eventData['end']) || empty($eventData['title'])) {
            throw new \InvalidArgumentException('Event data is incomplete.');
        }
    
        $startDate = new \DateTime($eventData['start']);
        $endDate = new \DateTime($eventData['end']);
        
        $startDateFormatted = $startDate->format('c'); 
        $endDateFormatted = $endDate->format('c'); 
    
        $event = [
            'summary' => $eventData['title'], 
            'location' => $eventData['location'] ?? 'Online', 
            'description' => $eventData['description'] ?? '',
            'start' => [
                'dateTime' => $startDateFormatted,
                'timeZone' => $eventData['timeZone'] ?? 'Europe/Paris', 
            ],
            'end' => [
                'dateTime' => $endDateFormatted,
                'timeZone' => $eventData['timeZone'] ?? 'Europe/Paris', 
            ],
            'reminders' => [
                'useDefault' => true, 
            ],
        ];
    
        $response = $this->httpClient->request('POST', $_ENV['GOOGLE_URL_EVENTS'], [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ],
            'json' => $event, 
        ]);
    
        return $response->toArray();
    }
    
    public function getEvents(string $accessToken): array
    {
        $url = $_ENV['GOOGLE_URL_EVENTS'];

        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to fetch events from Google Calendar');
        }

        return $response->toArray()['items'] ?? [];
    }
}
