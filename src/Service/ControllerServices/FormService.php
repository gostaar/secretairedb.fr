<?php 

namespace App\Service\ControllerServices;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Cache\CacheInterface;
use App\Entity\User;
use App\Model\SearchData;

class FormService
{
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private CacheInterface $cache;
    private RouteDataService $routeDataService;

    public function __construct(
        RouteDataService $routeDataService,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        CacheInterface $cache
    ) {
        $this->routeDataService = $routeDataService;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->cache = $cache;
    }

    public function createFormViews(Request $request, array $staticData, User $user, ?int $dossierId, ?string $fragment): array
    {
        $factureId = $request->query->get('facture');
        $devisId = $request->query->get('devis');

        $formViews = [];
        $dossierSearchResults = [];
        $repertoireSearchResults = [];
        $documentSearchResults = [];
        $ligneFactureSearchResults = [];
        $ligneDevisSearchResults = [];

        $routeConfig = $this->routeDataService->getRouteConfig();
        $config = $routeConfig[$fragment] ?? $this->routeDataService->createServiceConfig('Repertoire');
        $serviceName = $config['service'];

        foreach ($staticData as $key => $formClass) {
            if (strpos($key, 'add') === 0 && class_exists($formClass)) {
                $entityClass = $this->routeDataService->getFormEntityClass($key);
                if (class_exists($entityClass)) {
                    $entityId = $request->get('id', null);
                    $entityId
                        ? $entity = $this->entityManager->getRepository(\App\Entity\DocumentsUtilisateur::class)->find($entityId)
                        : $entity = new $entityClass();

                    if($entityClass === \App\Entity\DocumentsUtilisateur::class){
                        $form = $this->formFactory->create($formClass, $entity, [
                            'user' => $user,
                            'dossierId' => $dossierId ?? null,
                        ]); 
                    } else {
                        $form = $this->formFactory->create($formClass, $entity);
                    }

                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        if ($entity instanceof SearchData) {
                            $serviceName = $this->routeDataService->getFragmentMapping($fragment);

                            switch ($fragment) {
                                case 'link-Factures':
                                    $ligneFacture = $this->searchLignesFactures($entity, $user, $factureId);
                                    $ligneFactureSearchResults = empty($ligneFacture) ? 'vide' : $ligneFacture;
                                    break;
                                
                                case 'link-Devis':
                                    $ligneDevis = $this->searchLigneDevis($entity, $user, $devisId);
                                    $ligneDevisSearchResults = empty($ligneDevis) ? 'vide' : $ligneDevis;
                                    break;
                                
                                case 'link-PageDocument':
                                    $documents = $this->searchDocuments($entity, $user, $dossierId);
                                    $documentSearchResults = empty($documents) ? 'vide' : $documents;
                                    break;

                                case 'link-PageRepertoire':
                                    $repertoires = $this->searchRepertoires($entity, $user, $dossierId);
                                    $repertoireSearchResults = empty($repertoires) ? 'vide' : $repertoires;
                                    break;

                                default:
                                    $dossiers = $this->searchDossiers($entity, $user, $serviceName);
                                    $dossierSearchResults = empty($dossiers) ? 'vide' : $dossiers;
                                    break;
                            }
                        }
                    }

                    $formViews[$key] = $form->createView();
                }
            }
        }

        return [
            'formViews' => $formViews,
            'dossierSearchResults' => $dossierSearchResults,
            'repertoireSearchResults' => $repertoireSearchResults,
            'documentSearchResults' => $documentSearchResults,
            'ligneFactureSearchResults' => $ligneFactureSearchResults,
            'ligneDevisSearchResults' => $ligneDevisSearchResults,
        ];
    }

    private function searchDossiers(SearchData $entity, User $user, $serviceName): array
    {
        $service = $this->entityManager->getRepository(\App\Entity\Services::class)->getServiceByName($serviceName);
        $service->initializeRelations();

        return $this->entityManager
            ->getRepository(\App\Entity\Dossier::class)
            ->findBySearch($entity, $user, $service->getDossiers());
    }

    private function searchDocuments(SearchData $entity, User $user, ?int $dossierId): array
    {
        return $this->entityManager
            ->getRepository(\App\Entity\DocumentsUtilisateur::class)
            ->findBySearch($entity, $user, $dossierId);
    }

    private function searchRepertoires(SearchData $entity, User $user, ?int $dossierId): array
    {
        return $this->entityManager
            ->getRepository(\App\Entity\Repertoire::class)
            ->findBySearch($entity, $user, $dossierId);
    }

    private function searchLignesFactures(SearchData $entity, User $user, ?int $factureId): array
    {
        return $this->entityManager
            ->getRepository(\App\Entity\FactureLigne::class)
            ->findBySearch($entity, $user, $factureId);
    }
    
    private function searchLigneDevis(SearchData $entity, User $user, ?int $devisId): array
    {
        return $this->entityManager
            ->getRepository(\App\Entity\DevisLigne::class)
            ->findBySearch($entity, $user, $devisId);
    }
}
