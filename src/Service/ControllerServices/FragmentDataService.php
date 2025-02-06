<?php
namespace App\Service\ControllerServices;

use App\Repository\DossierRepository;
use App\Repository\ServicesRepository;
use App\Repository\DocumentsUtilisateurRepository;
use App\Repository\RepertoireRepository;
use App\Repository\FactureRepository;
use App\Repository\DevisRepository;
use App\Repository\FactureLigneRepository;
use App\Repository\DevisLigneRepository;
use App\Repository\IdentifiantsRepository;
use App\Repository\GoogleTokenRepository;
use App\Repository\EventsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Google_Client;
use Google_Service_Calendar;

class FragmentDataService
{
    private RepertoireRepository $repertoireRepository;
    private DossierRepository $dossierRepository;
    private ServicesRepository $serviceRepository;
    private DocumentsUtilisateurRepository $documentsRepository;
    private DevisRepository $devisRepository;
    private FactureLigneRepository $factureLigneRepository;
    private DevisLigneRepository $devisLigneRepository;
    private IdentifiantsRepository $identifiantsRepository;
    private EventsRepository $eventsRepository;
    private GoogleTokenRepository $googleTokenRepository;

    public function __construct(
        RepertoireRepository $repertoireRepository,
        DossierRepository $dossierRepository,
        ServicesRepository $serviceRepository,
        DocumentsUtilisateurRepository $documentsRepository,
        FactureRepository $factureRepository,
        FactureLigneRepository $factureLigneRepository,
        DevisRepository $devisRepository,
        DevisLigneRepository $devisLigneRepository,
        IdentifiantsRepository $identifiantsRepository,
        EventsRepository $eventsRepository,
        GoogleTokenRepository $googleTokenRepository,
        string $googleCredentials,
    ) {
        $this->repertoireRepository = $repertoireRepository;
        $this->dossierRepository = $dossierRepository;
        $this->serviceRepository = $serviceRepository;
        $this->documentsRepository = $documentsRepository;
        $this->factureRepository = $factureRepository;
        $this->factureLigneRepository = $factureLigneRepository;
        $this->devisRepository = $devisRepository;
        $this->devisLigneRepository = $devisLigneRepository;
        $this->identifiantsRepository = $identifiantsRepository;
        $this->eventsRepository = $eventsRepository;
        $this->googleTokenRepository = $googleTokenRepository;
        $this->googleCredentials = $googleCredentials;
    }

    public function getFragmentData(?int $dossierId, $user, ?int $documentId, ?int $repertoireId, ?string $service): array
    {
        $services = $this->serviceRepository->getUserServices($user->getId());
        $dossiers = $service !== null ? $this->dossierRepository->getUserDossiers($user->getId(), $service) : null;

        $repertoires = $dossierId === null ? null : $this->repertoireRepository->getUserDossierRepertoires($user->getId(), $dossierId);
        $repertoire = $repertoireId === null ? null : $this->repertoireRepository->find($repertoireId);
        $documents = $dossierId === null ? null : $this->documentsRepository->getUserDossierDocument($user->getId(), $dossierId);
        $document = $documentId === null ? null : $this->documentsRepository->find($documentId);
        $events = $service === null ? null : $this->eventsRepository->getUserEvents($user->getId(), $service);
        $dossier = $dossierId === null ? null : $this->dossierRepository->find($dossierId);
        $facturesRepo = $this->factureRepository->getUserFactures($user->getId());
        $factures = array_map(function($facture) {
            $factureArray = $facture->toArray();
            $factureArray['lignes'] = $this->factureLigneRepository->findByFacture($facture->getId(), "1");
            return $factureArray;
        }, $facturesRepo);        
        $devisRepo = $this->devisRepository->getUserDevis($user->getId());
        $devis = array_map(function($devis) {
            $devisArray = $devis->toArray();
            $devisArray['lignes'] = $this->devisLigneRepository->findByDevis($devis->getId(), "1");
            return $devisArray;
        }, $devisRepo);
        $identifiants = $this->identifiantsRepository->getUserIdentifiants($user->getId());
        
        //Google
        $isTokenValid = false;
        $googleToken = $this->googleTokenRepository->findValidTokenByUser($user);
       

        return [
            'services' => $services,
            'dossiers' => $dossiers,
            'dossier' => $dossier,
            'documents' => $documents,
            'document' => $document,
            'repertoires' => $repertoires,
            'repertoire' => $repertoire,
            'user' => $user,
            'factures' => $factures,
            'devis' => $devis,
            'identifiants' => $identifiants,
            'events' => $events,
            'isTokenValid' => $isTokenValid,
        ];
    }

    public function fetchGoogleCalendarEvents(string $accessToken): array
    {
        $client = new Google_Client();
        $client->setAuthConfig($this->googleCredentials);
        $client->setScopes(['https://www.googleapis.com/auth/calendar']);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setAccessToken($accessToken);

        $calendarService = new Google_Service_Calendar($client);
        $events = $calendarService->events->listEvents('primary', [
            'timeMin' => date('c'),
            'maxResults' => 10,
            'singleEvents' => true,
            'orderBy' => 'startTime'
        ]);

        return array_map(function ($event) {
            return [
                'id' => $event->getId(),
                'summary' => $event->getSummary(),
                'start' => $event->getStart()->getDateTime() ?? $event->getStart()->getDate(),
                'end' => $event->getEnd()->getDateTime() ?? $event->getEnd()->getDate(),
                'location' => $event->getLocation(),
                'description' => $event->getDescription(),
            ];
        }, $events->getItems());
    }
}