<?php

// src/Twig/FetchExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\Exception\ExceptionInterface;

class FetchExtension extends AbstractExtension
{
    private $client;

    // Injecter le service HttpClient
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('fetch', [$this, 'fetchFilter']),
        ];
    }

    /**
     * Le filtre 'fetch' effectue une requête HTTP et récupère le contenu de l'URL.
     *
     * @param string $url L'URL à récupérer
     * @return mixed Le contenu de la réponse (par exemple, un tableau JSON)
     */
    public function fetchFilter(string $url)
    {
        try {
            // Effectuer la requête GET
            $response = $this->client->request('GET', $url);

            // Récupérer la réponse au format JSON (ou autre selon votre API)
            $data = $response->toArray(); // Si la réponse est en JSON
            return $data;
        } catch (ExceptionInterface $e) {
            // Gérer les erreurs (ex. l'URL est invalide ou l'API échoue)
            return ['error' => $e->getMessage()];
        }
    }
}
