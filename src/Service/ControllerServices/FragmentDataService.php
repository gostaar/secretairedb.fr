<?php
namespace App\Service\ControllerServices;

use App\Repository\DossierRepository;
use App\Repository\ServicesRepository;
use App\Repository\DocumentsUtilisateurRepository;
use App\Repository\RepertoireRepository;
use App\Repository\FactureRepository;
use App\Repository\DevisRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class FragmentDataService
{
    private FormService $formService;
    private RedisService $redisService;
    private RouteDataService $routeDataService;
    private RepertoireRepository $repertoireRepository;
    private DossierRepository $dossierRepository;
    private ServicesRepository $serviceRepository;
    private DocumentsUtilisateurRepository $documentsRepository;
    private FactureRepository $factureRepository;
    private DevisRepository $devisRepository;

    public function __construct(
        FormService $formService,
        RedisService $redisService,
        RouteDataService $routeDataService,
        RepertoireRepository $repertoireRepository,
        DossierRepository $dossierRepository,
        ServicesRepository $serviceRepository,
        DocumentsUtilisateurRepository $documentsRepository,
        FactureRepository $factureRepository,
        DevisRepository $devisRepository,
    ) {
        $this->formService = $formService;
        $this->redisService = $redisService;
        $this->routeDataService = $routeDataService;
        $this->repertoireRepository = $repertoireRepository;
        $this->dossierRepository = $dossierRepository;
        $this->serviceRepository = $serviceRepository;
        $this->documentsRepository = $documentsRepository;
        $this->factureRepository = $factureRepository;
        $this->devisRepository = $devisRepository;
    }

    public function getFragmentData(Request $request, string $fragment, ?int $dossierId, $user, $documentId): array
    {
        $this->updateNavigationSession($request, $fragment, $dossierId);

        $staticData = $this->getStaticDataFromCache($user, $fragment);
        $dynamicData = $this->getDynamicDataFromCache($fragment, $dossierId);

        $fragmentMapping = $this->routeDataService->getFragmentMapping($fragment);

        $services = $this->serviceRepository->getUserServices($user->getId());
        $dossiers = $this->dossierRepository->getUserDossiers($user->getId(), $fragmentMapping);
        $repertoires = $dossierId === null ? null : $this->repertoireRepository->getUserDossierRepertoires($user->getId(), $dossierId);
        $documents = $dossierId === null ? null : $this->documentsRepository->getUserDossierDocument($user->getId(), $dossierId);
        $document = $documentId === null ? null : $this->documentsRepository->find($documentId);
        $dossier = $dossierId === null ? null : $this->dossierRepository->find($dossierId);
        $facturesRepo = $this->factureRepository->getUserFactures($user->getId());
        $factures = array_map(fn($facture) => $facture->toArray(), $facturesRepo);
        $devisRepo = $this->devisRepository->getUserDevis($user->getId());
        $devis = array_map(fn($devi) => $devi->toArray(), $devisRepo);
        
        return array_merge($staticData, $dynamicData, [
            'services' => $services,
            'dossiers' => $dossiers,
            'dossier' => $dossier,
            'documents' => $documents,
            'document' => $document,
            'repertoires' => $repertoires,
            'user' => $user,
            'factures' => $factures,
            'devis' => $devis,
            'ligneFacture' => '',
            'ligneDevis' => '',
        ]);
    }

    private function updateNavigationSession(Request $request, string $fragment, ?int $dossierId): void
    {
        $request->getSession()->set('previous_fragment', $fragment);
        $request->getSession()->set('previous_dossier_id', $dossierId);
    }

    private function getStaticDataFromCache($user, $fragment): array
    {
        $cacheKey = 'staticData_' . md5($user->getId());
        $staticData = $this->redisService->get($cacheKey);

        if (!$staticData) {
            $staticData = $this->routeDataService->getStaticData($user, $fragment);
            $this->redisService->set($cacheKey, serialize($staticData));
        } else {
            $staticData = unserialize($staticData);
        }

        return $staticData;
    }

    private function getDynamicDataFromCache(string $fragment, ?int $dossierId): array
    {
        $cacheKey = 'dynamicData_' . md5($fragment . $dossierId);
        $cachedData = $this->redisService->get($cacheKey);

        if (!$cachedData) {
            $dynamicData = $this->routeDataService->getFormData($fragment);
            $this->redisService->set($cacheKey, serialize($dynamicData));
        } else {
            $dynamicData = unserialize($cachedData);
        }

        return $dynamicData;
    }

}