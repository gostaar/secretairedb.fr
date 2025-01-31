<?php 

namespace App\Service\ControllerServices;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Cache\CacheInterface;
use App\Entity\User;
use App\Model\SearchData;
use App\Model\ChangePasswordModel;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;


class FormService extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private CacheInterface $cache;
    private RouteDataService $routeDataService;
    private UserPasswordHasherInterface $passwordHasher;
    private $logger;
    private UserRepository $userRepository;
    private ImageRepository $imageRepository;

    public function __construct(
        RouteDataService $routeDataService,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        CacheInterface $cache,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $logger,
        UserRepository $userRepository,
        ImageRepository $imageRepository,
    ) {
        $this->routeDataService = $routeDataService;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->cache = $cache;
        $this->passwordHasher = $passwordHasher;
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->imageRepository = $imageRepository;
    }

    public function createFormViews(Request $request, array $staticData, User $user, ?int $dossierId, ?string $fragment, $documentId, $repertoireId): array
    {
        $userInitialized = $this->userRepository->find($user);
        
        $factureId = $request->query->get('facture');
        $devisId = $request->query->get('devis');

        $formViews = [];
        $searchResults = [];

        $routeConfig = $this->routeDataService->getRouteConfig();
        $config = $routeConfig[$fragment] ?? $this->routeDataService->createServiceConfig('Repertoire');
        $serviceName = $config['serviceLink'];

        foreach ($staticData as $key => $formClass) {
            if (strpos($key, 'add') === 0 && class_exists($formClass)) {
                
                $entityClass = $this->routeDataService->getFormEntityClass($key);  // Entity class
                if (class_exists($entityClass)) {
                    // Vérifie si l'entité demandée fait partie de celles associées au fragment
                    // $entities = $this->routeDataService->getFragmentEntityMapping($fragment);
                    // if (!in_array($entityClass, $entities)) {continue; }
                    $args = [];

                    // Logique pour récupérer ou créer l'entité en fonction du fragment
                    switch ($entityClass) {
                        case "App\Entity\Repertoire":
                            $entity = $repertoireId ? $this->entityManager->getRepository(\App\Entity\Repertoire::class)->find($repertoireId) : new $entityClass();
                            break;
                            
                        case "App\Entity\DocumentsUtilisateur":
                            $entity = $documentId ? $this->entityManager->getRepository(\App\Entity\DocumentsUtilisateur::class)->find($documentId) : new $entityClass();
                            $args = ['user' => $user,'documentId' => $documentId ?? null];
                            break;
                            
                        case "App\Entity\User":
                            $entity = $user;
                            break;
                        
                        default:
                        $entity = new $entityClass();
                    }
                    
                    $form = empty($args) 
                        ? $this->formFactory->create($formClass, $entity)
                        : $this->formFactory->create($formClass, $entity, $args);

                    $form->handleRequest($request);
                    // if($formClass = "App\Form\DocumentsUtilisateurType"){dd($form);}
                    // if ($form->isSubmitted()) {
                    //     dd($form->get('images')->getData());  // Ou var_dump($form->getData()); pour afficher les données du formulaire
                    // }
                    if ($form->isSubmitted() && $form->isValid()) {
                        switch($entity){
                            case $entity instanceof SearchData:
                                $searchResults = array_merge($searchResults, $this->handleSearchData($entity, $fragment, $dossierId, $user));
                                break;
                            case $entity instanceof ChangePasswordModel:
                                $this->handleChangePasswordModel($entity, $form, $user);
                                break;
                            case $entity instanceof \App\Entity\Identifiants:
                                $userInitialized->addIdentifiant($entity);
                                $this->entityManager->flush();
                                break;
                            case $entity instanceof \App\Entity\User: 
                                $userInitialized->setNom($entity->getNom());
                                $userInitialized->setAdresse($entity->getAdresse());
                                $userInitialized->setCodePostal($entity->getCodePostal());
                                $userInitialized->setVille($entity->getVille());
                                $userInitialized->setPays($entity->getPays());
                                $userInitialized->setTelephone($entity->getTelephone());
                                $userInitialized->setMobile($entity->getMobile());
                                $userInitialized->setEmail($entity->getEmail());
                                $userInitialized->setSiret($entity->getSiret());
                                $userInitialized->setNomEntreprise($entity->getNomEntreprise());
                                $this->entityManager->flush();
                                break;
                            case $entity instanceof \App\Entity\Dossier:
                                $services =  $this->entityManager->getRepository(\App\Entity\Services::class)->getServiceByName($serviceName);
                                $entity->setServices($services);
                                $userInitialized->addDossier($entity);
                                $this->entityManager->flush();
                                break;
                            case $entity instanceof \App\Entity\DocumentsUtilisateur:
                                if($formClass === "App\Form\AddDocumentsUtilisateurType"){
                                    $dossier =  $this->entityManager->getRepository(\App\Entity\Dossier::class)->find($dossierId);
                                    $entity->setDossier($dossier);
                                    $userInitialized->addDocument($entity);
                                    $this->entityManager->flush();
                                } else {
                                    // foreach ($entity->getImages() as $image) {
                                    //     $image->setDocumentUtilisateur($entity);
                                    // }
                                    // dd($entity);
                                    $this->entityManager->flush();
                                }
                                break;
                            default:
                            // dd($form);
                                $this->entityManager->flush();
                        }
                    }

                    $formViews[$key] = $form->createView(); 
                }
            }
        }

        return [
            'formViews' => $formViews,
            'searchResults' => $searchResults,
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

    private function handleSearchData(SearchData $entity, string $fragment, ?int $dossierId, User $user): array
    {
        $result = [];

        switch ($fragment) {
            case 'link-PageDocument':
                $documents = $this->searchDocuments($entity, $user, $dossierId);
                $result['documentSearchResults'] = empty($documents) ? 'vide' : $documents;
                break;

            case 'link-PageRepertoire':
                $repertoires = $this->searchRepertoires($entity, $user, $dossierId);
                $result['repertoireSearchResults'] = empty($repertoires) ? 'vide' : $repertoires;
                break;

            default:
                $dossiers = $this->searchDossiers($entity, $user, $this->routeDataService->getFragmentMapping($fragment));
                $result['dossierSearchResults'] = empty($dossiers) ? 'vide' : $dossiers;
                break;
        }

        return $result;
    }

    private function handleChangePasswordModel(ChangePasswordModel $entity, FormInterface $form, User $user): void
    {
        $currentPassword = $form->get('currentPassword')->getData();
        $newPassword = $form->get('newPassword')->getData();
        $confirmPassword = $form->get('confirmPassword')->getData();

        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            $this->addFlash('error', 'Votre mot de passe actuel est incorrect.');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            return;
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $this->userRepository->updatePassword($user, $hashedPassword);

        $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');
    }

    private function handleForm(FormInterface $form, $repository, string $function, $entity, $formClass)
    {
        $imageFile = $form->get('imageFile')->getData();
        if ($imageFile === null) {
            // Si il n'y a pas de nouveau fichier, on peut vider le champ imageFile de l'entité
            $entity->setImageFile(null);
        }
        call_user_func([$repository, $function], $form);
        $form = $this->formFactory->create($formClass, $entity);
        // $form->get('imageFile')->setData(null);
        // dd($form->getData());
        $this->addFlash('success', 'Les données ont été mises à jour avec succès.');
    }
}
