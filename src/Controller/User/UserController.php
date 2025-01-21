<?php 
namespace App\Controller\User;

use App\Service\RouteDataService;
use App\Service\RedisService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserController extends AbstractController
{
    private RouteDataService $routeDataService;
    private RedisService $redisService;
    private $entityManager;
    private $httpClient;

    public function __construct(
        RouteDataService $routeDataService,
        RedisService $redisService,
        EntityManagerInterface $entityManager,
        HttpClientInterface $httpClient,
    ){
        $this->routeDataService = $routeDataService;
        $this->redisService = $redisService;
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
    }

    private function getUserFromCache(): ?User
    {   
        $cachedKeys = $this->redisService->keys("user*");
        $user = null;
        if (!empty($cachedKeys)) {
            $cachedKey = $cachedKeys[0];
            $cachedUser = $this->redisService->get($cachedKey);
            $user = unserialize($cachedUser);
           
        } else {
            $user = $this->getUser();
            $key = 'user' . md5($user->getEmail());
            $user->initializeRelations();
            $userData = $user->toArray();
            $userSerialized = serialize($userData);
            $this->redisService->set($key, $userSerialized);
        }
        return $user;
    }

    private function getCacheData(Request $request, string $fragment, ?int $dossierId): array
    {
        // $user = $this->getUserFromCache();
        $user = $this->getUser();
        $previousFragment = $request->getSession()->get('previous_fragment');
        $fragmentHasChanged = $fragment !== $previousFragment;
        $request->getSession()->set('previous_fragment', $fragment);
    
        $previousDossierId = $request->getSession()->get('previous_dossier_id');
        $dossierIdHasChanged = $dossierId !== $previousDossierId;
        $request->getSession()->set('previous_dossier_id', $dossierId);
    

        $staticDataCacheKey = 'staticData_' . md5($user->getId());
        $staticData = $this->redisService->get($staticDataCacheKey);
    
        if (!$staticData) {
            $staticData = $this->routeDataService->getStaticData($user, $fragment);
            $this->redisService->set($staticDataCacheKey, serialize($staticData)); 
        } else {
            $staticData = unserialize($this->redisService->get($staticDataCacheKey));    
        }
        // $formViews = [];
        // $dS = [];
        // $rS = [];
        // $docS = [];

        // $routeConfig = $this->routeDataService->getRouteConfig();
        // $config = $routeConfig[$fragment] ?? $this->routeDataService->createServiceConfig('Repertoire');
        // $serviceName = $config['service'];

        // foreach ($staticData as $key => $value) {
        //     if (strpos($key, 'add') === 0) {
        //         if (class_exists($value)) {
        //             $entityClass = $this->routeDataService->getFormEntityClass($key);
        //             if (class_exists($entityClass)) {
        //                 $id = $request->get('id', null);
        //                 $id ? $entity = $this->entityManager->getRepository(\App\Entity\DocumentsUtilisateur::class)->find($id) : $entity = new $entityClass();
        //                 if($entityClass === \App\Entity\DocumentsUtilisateur::class){
        //                     $form = $this->createForm($value, $entity, [
        //                         'user' => $user,
        //                         'dossierId' => $dossierId ?? null,
        //                     ]); 
        //                 } else {
        //                     $form = $this->createForm($value, $entity);
        //                 }
                        
        //                 $form->handleRequest($request);
        //                 if ($form->isSubmitted() && $form->isValid()) {
        //                     if($entity instanceOf \App\Model\SearchData){
        //                         $service = $user->getServices()->filter(fn($s) => $s->getName() === $serviceName)->first();

        //                         switch ($fragment) {
        //                             case 'link-Repertoire':
        //                             case 'link-Administratif':
        //                             case 'link-Commercial':
        //                             case 'link-Numerique':
        //                             case 'link-Telephone':
        //                                 $dossiers = $this->entityManager->getRepository(\App\Entity\Dossier::class)->findBySearch($entity, $user, $service->getDossiers());
        //                                 if(empty($dossiers)){ $dS = 'vide'; } else { foreach ($dossiers as $dossier) { $dS[] = $dossier; } };
        //                                 break;

        //                             case 'link-PageDocument':
        //                                 $documents = $this->entityManager->getRepository(\App\Entity\DocumentsUtilisateur::class)->findBySearch($entity, $user, $dossierId);
        //                                 if(empty($documents)){ $docS = 'vide'; } else { foreach ($documents as $document) { $docS[] = $document; } };
        //                                 break;

        //                             case 'link-PageRepertoire':
        //                                 $repertoires = $this->entityManager->getRepository(\App\Entity\Repertoire::class)->findBySearch($entity, $user, $dossierId);
        //                                 if(empty($repertoires)){ $rS = 'vide'; } else { foreach ($repertoires as $repertoire) { $rS[] = $repertoire; } };
        //                                 break;
        //                             }
        //                     }
        //                 }
                            
        //                 $formViews[$key] = $form->createView(); 
        //             } else {
        //                 throw new \Exception("La classe {$entityClass} n'existe pas.");
        //             }
        //         }
        //     }
        // }
        //-----------------------------------------------------------------------------------------------------
        $urlServices = $this->generateUrl('api_services_get_collection', ['users' => $user->getId()]);
        // $urlServices = $this->generateUrl('https://127.0.0.1:8000/api/services', [
        //     'users' => $user->getId()
        // ]);

        $response = $this->httpClient->request('GET', $urlServices);

        // // Récupérer les données JSON
        $data = $response->toArray();  // Ou $response->getContent() si ce n'est pas du JSON
       

        // // Afficher l'URL générée
        dd($data);
        // $responseService = $this->client->request('GET', $url);
        // $services = $responseService->toArray();
        
        // $urlDossiers = $this->generateUrl('http://localhost:8000/api/dossiers?user=' . $user->getId());
        // $responseDossiers = $this->client->request('GET', $url);
        // $dossiers = $responseDossiers->toArray();

        // $formViews = [
        //     'services' => $services,
        //     'dossiers' => $dossiers
        // ];

        $dynamicDataCacheKey = 'dynamicData_' . md5($fragment . $dossierId);
        $dynamicData = $this->redisService->get($dynamicDataCacheKey);

        if ($fragmentHasChanged || $dossierIdHasChanged) {
            $this->redisService->delete($dynamicDataCacheKey); 
            $dynamicData = $this->routeDataService->getFormData($fragment); 
            $this->redisService->set($dynamicDataCacheKey, serialize($dynamicData)); 
        } elseif (!$dynamicData) {
            $dynamicData = $this->routeDataService->getFormData($fragment);
            $this->redisService->set($dynamicDataCacheKey, serialize($dynamicData));
        } else {
            $dynamicData = unserialize($dynamicData); 
        }
        return array_merge($staticData, $dynamicData, ['formViews' => $formViews], 
            // ['dS' => $dS], ['rS' => $rS], ['docS' => $docS]
        );
    }
    
    #[Route('/user', name: 'user')]
    public function index(Request $request): Response
    {
        $fragment = $request->query->get('fragment', 'link-Acceuil');
        $dossierId = $request->query->get('dossier');     

        $formData = $this->getCacheData($request, $fragment, $dossierId);

        // if($formData['dS']){ $formData['dS'] === 'vide' ? $formData['dossiers'] = null : $formData['dossiers'] = $formData['dS'];}
        // if($formData['rS']){ $formData['rS'] === 'vide' ? $formData['repertoires'] = null : $formData['repertoires'] = $formData['rS'];}
        // if($formData['docS']){ $formData['docS'] === 'vide' ? $formData['documents'] = null : $formData['documents'] = $formData['docS'];}

        return $this->render('userPage/user.html.twig', $formData);
    }
    
    #[Route('/changefragment', name: 'changefragment')]
    public function changefragment(Request $request): Response
    {   
        $fragment = $request->query->get('fragment', 'link-Acceuil');
        $dossierId = (int) $request->query->get('dossier');     

        $formData = $this->getCacheData($request, $fragment, $dossierId);



        // if($formData['dS']){ $formData['dS'] === 'vide' ? $formData['dossiers'] = null : $formData['dossiers'] = $formData['dS'];}
        // if($formData['rS']){ $formData['rS'] === 'vide' ? $formData['repertoires'] = null : $formData['repertoires'] = $formData['rS'];}
        // if($formData['docS']){ $formData['docS'] === 'vide' ? $formData['documents'] = null : $formData['documents'] = $formData['docS'];}

        return $this->json([
            'fragmentContent' => $this->renderView('userPage/_fragmentContent.html.twig', $formData),
        ]);
    }

    private function mapEntitiesToArray($entities): array
    {
        return array_map(fn($entity) => $entity->toArray(), $entities->toArray());
    }
}
