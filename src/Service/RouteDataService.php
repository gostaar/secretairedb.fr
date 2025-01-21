<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class RouteDataService
{
    private $formFactory;
    private $entityManager;

    public function __construct( FormFactoryInterface $formFactory, EntityManagerInterface $entityManager ){
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
    }

    // public function getFormData($user, string $fragment, ?int $dossierId = null, Request $request,): array
    public function getFormData(string $fragment): array
    {
        // Récupération de la configuration de la route
        $routeConfig = $this->getRouteConfig();
        $config = $routeConfig[$fragment] ?? $this->createServiceConfig('Repertoire');
        
        // Mapping des templates
        $templateMapping = $this->getTemplateMapping();
        $fragmentTemplate = $templateMapping[$fragment] ?? 'userPage/_mainContent.html.twig';
        
        //Filtre par service
        // $serviceName = $config['service'];
        // $this->filterByService($config, $user, $serviceName, $fragment, $dossierId, $request); 
        
        // Ajout des sections et du template
        $config['fragment'] = $fragment;
        $config['fragmentTemplate'] = $fragmentTemplate;

        return $config;
    }
    
    public function getRouteConfig(): array
    {
        return [
            'link-Acceuil' => $this->createServiceConfig('Repertoire'),
            'link-Administratif' => $this->createServiceConfig('Administratif'),
            'link-Agenda' => $this->createServiceConfig('Agenda'),
            'link-Commercial' => $this->createServiceConfig('Commercial'),
            'link-Numerique' => $this->createServiceConfig('Numerique'),
            'link-Telephone' => $this->createServiceConfig('Telephonique'),
            'link-espacepersonnel' => $this->createServiceConfig('Repertoire'),
            'link-Profile' => $this->createServiceConfig('Repertoire'), 
            'link-Factures' => $this->createServiceConfig('Repertoire'),
            'link-Repertoire' => $this->createServiceConfig('Repertoire'),
            'link-parametres' => $this->createServiceConfig('Repertoire'),
            'link-PageRepertoire' => $this->createServiceConfig('Repertoire'),
            'link-MainAgenda' => $this->createServiceConfig('Agenda'),
            'link-PageAgenda' => $this->createServiceConfig('Agenda'),
            'link-PageFacture' => $this->createServiceConfig('Repertoire'),
            'link-PageDevis' => $this->createServiceConfig('Repertoire'),
        ];
    }

    public function getStaticData($user, $fragment)
    {
        return $staticData = [
            // 'services' => $user->getServices(),
            'sections' => $fragment,
            'addContact' => \App\Form\ContactType::class,
            'addDevis' => \App\Form\DevisType::class,
            'addDevisLigne' => \App\Form\DevisLigneType::class,
            'addDevisVersion' => \App\Form\DevisVersionType::class,
            'addDocument' => \App\Form\DocumentsUtilisateurType::class,
            'addDossier' => \App\Form\DossierType::class,
            'addEvents' => \App\Form\EventsType::class,
            'addFactureLigne' => \App\Form\FactureLigneType::class,
            'addFacture' => \App\Form\FactureType::class,
            'addRepertoire' => \App\Form\RepertoireType::class,
            'addServices' => \App\Form\ServicesType::class,
            'addTypeDocument' => \App\Form\TypeDocumentType::class,
            'addUserForm' => \App\Form\UserType::class, 
            'addSearchData' => \App\Form\SearchType::class
        ];
    }

    public function getFormEntityClass(string $key): string
    {
        $formEntityClasses = [
            'addContact' => \App\Entity\Contact::class,
            'addDevis' => \App\Entity\Devis::class,
            'addDevisLigne' => \App\Entity\DevisLigne::class,
            'addDevisVersion' => \App\Entity\DevisVersion::class,
            'addDocument' => \App\Entity\DocumentsUtilisateur::class,
            'addDossier' => \App\Entity\Dossier::class,
            'addEvents' => \App\Entity\Events::class,
            'addFactureLigne' => \App\Entity\FactureLigne::class,
            'addFacture' => \App\Entity\Facture::class,
            'addRepertoire' => \App\Entity\Repertoire::class,
            'addServices' => \App\Entity\Services::class,
            'addTypeDocument' => \App\Entity\TypeDocument::class,
            'addUserForm' => \App\Entity\User::class,
            'addSearchData' => \App\Model\SearchData::class,
        ];
    
        return $formEntityClasses[$key] ?? \App\Entity\User::class;
    }

    public function getTemplateMapping(): array
    {
        return [
            // 'link-Administratif' => 'partials/user/Administratif/_documents.html.twig',
            // 'link-Commercial' => 'partials/user/Commercial/_commercial.html.twig',
            // 'link-Numerique' => 'partials/user/Numerique/_numerique.html.twig',
            // 'link-Repertoire' => 'partials/user/Profile/_repertoire.html.twig',
            // 'link-Telephone' => 'partials/user/Telephone/_telephone.html.twig',
            'link-Acceuil' => 'userPage/_mainContent.html.twig',
            'link-newDocument' => 'partials/user/Administratif/_newDocuments.html.twig',
            'link-Agenda' => 'partials/user/Agenda/_agenda.html.twig',
            'link-MainAgenda' => 'partials/user/Agenda/_agendaMain.html.twig',
            'link-PageAgenda' => 'partials/user/Agenda/index.html.twig',
            'link-Profile' => 'partials/user/EspacePersonnel/profile.html.twig',
            'link-espacepersonnel' => 'partials/user/EspacePersonnel/index.html.twig',
            'link-Factures' => 'partials/user/EspacePersonnel/FactureDevis/factures.html.twig',
            'link-PageFacture' => 'partials/user/Profile/facture.html.twig',
            'link-parametres' => 'partials/user/EspacePersonnel/parametres.html.twig',
            'link-PageDossier' => 'partials/user/dossier.html.twig',
            
            'link-Administratif' => 'partials/user/Dossiers/index.html.twig',
            'link-Commercial' => 'partials/user/Dossiers/index.html.twig',
            'link-Numerique' => 'partials/user/Dossiers/index.html.twig',
            'link-Repertoire' => 'partials/user/Dossiers/index.html.twig',
            'link-Telephone' => 'partials/user/Dossiers/index.html.twig',
            'link-PageRepertoire' => 'partials/user/EspacePersonnel/Repertoire/repertoire.html.twig',
            'link-PageDocument' => 'partials/user/document.html.twig',
            'link-Devis' => 'partials/user/EspacePersonnel/FactureDevis/devis.html.twig',
        ];
    }

    // public function filterByService(array &$config, $user, $serviceName, $fragment, ?int $dossierId = null, $request)
    // {
    //     if($dossierId) {
    //         $servicen = $this->entityManager->getRepository(\App\Entity\Dossier::class)->findServiceByDossierId($dossierId);
    //         $serviceName = $servicen['name'];
    //         $config['service'] = $serviceName;
    //     }
                
    //     $service = $this->getServiceByName($user, $serviceName);
    //     if (!$service) {
    //         return;
    //     }

    //     switch ($serviceName) {
    //         case 'Agenda':
    //             $config = $this->handleAgenda($service);
    //             break;
        
    //         case 'Administratif':
    //         case 'Commercial':
    //         case 'Numerique':
    //         case 'Telephonique':
    //             $config = $this->handleDossiers($service, $user, $dossierId);
    //             break;
        
    //         case 'Repertoire':
    //             $config = $this->handleRepertoire($service, $user, $dossierId);
    //             break;
        
    //         default:
    //             $config = [];
    //             break;
    //     }       
    // }

    // private function handleDossiers($service, $user, $dossierId)
    // {
    //     $config['title'] = 'Gestion ' . $service->getName(); // Dynamique
    //     $config['service'] = $service->getName();
    //     $dossiers = $service->getDossiers()->filter(fn($dossier) => $dossier->getUser() === $user);
        
    //     $config['dossiers'] = $dossiers;
        

    //     if ($dossierId) {
    //         $config['dossier'] = $service->getDossiers()->filter(fn($dossier) => $dossier->getId() === $dossierId)->first();

    //         if (!$config['dossier'] || $config['dossier']->getUser() !== $user) {
    //             throw new \Exception("Dossier introuvable ou vous n'avez pas l'autorisation d'y accéder.");
    //         }

    //         $documents = $this->entityManager
    //             ->getRepository(\App\Entity\DocumentsUtilisateur::class)
    //             ->findByDossierId($dossierId);

    //         $config['documents'] = $documents;
    //     }

    //     $config['typeDocuments'] = $user->getTypeDocuments();
    //     return $config;
    // }

    // private function handleRepertoire($service, $user, $dossierId)
    // {
    //     $config = $this->handleDossiers($service, $user, $dossierId); // Réutilise la logique
    //     $config['contacts'] = $user->getContacts();
    //     $config['factures'] = $this->handleFacture($user);
    //     $config['devis'] = $this->handleDevis($user);

    //     if ($dossierId) {
    //         $repertoires = $this->entityManager
    //             ->getRepository(\App\Entity\Repertoire::class)
    //             ->findByDossierId($dossierId, $user);

    //         $config['repertoires'] = $repertoires;   
    //     }       

    //     return $config;
    // }

    // private function handleAgenda($service)
    // {
    //     return [
    //         'events' => $service->getEvents(),
    //     ];
    // }

    // private function handleFacture($user)
    // {
    //     $factures = $this->mapEntitiesToArray($user->getFactures());
        
    //     foreach ($factures as &$facture) {
    //         $factureId = $facture['id'];
    //         $factureLignes = $this->entityManager->getRepository(\App\Entity\FactureLigne::class)->findByFacture($factureId, $user);
    //         $montantTotal = 0;
    //         foreach ($factureLignes as $factureLigne) {
    //             $montantTotal += $factureLigne['totalMontant'];
    //         }

    //         $facture['montant'] = $montantTotal; 
    //     }
        
    //     return $factures;
    // }

    // private function handleDevis($user)
    // {
    //     $devis = $this->mapEntitiesToArray($user->getDevis());
        
    //     foreach ($devis as &$devi) {
    //         $devisId = $devi['id'];
    //         $devisLignes = $this->entityManager->getRepository(\App\Entity\DevisLigne::class)->findByDevis($devisId, $user);
    //         $montantTotal = 0;
    //         foreach ($devisLignes as $devisLigne) {
    //             $montantTotal += $devisLigne['totalMontant'];
    //         }

    //         $devi['montant'] = $montantTotal; 
    //     }
        
    //     return $devis;
    // }

    // private function searchData($elements, $searchQuery) {
    //     $elements = $elements->toArray();
    //     $elements = array_filter($elements, function ($e) use ($searchQuery) {
    //         return stripos($e->getName(), $searchQuery) !== false;
    //     });

    //     return $elements;
    // }

    // private function getServiceByName($user, $serviceName)
    // {
    //     return $user->getServices()->filter(fn($s) => $s->getName() === $serviceName)->first();
    // }

    // private function mapEntitiesToArray($entities): array
    // {
    //     return array_map(fn($entity) => $entity->toArray(), $entities->toArray());
    // }
    
    public function createServiceConfig(string $service): array
    {
        return ['service' => $service];
    }

    // private function getLastServiceName($user): ?string
    // {
    //     // Logique pour récupérer le dernier service enregistré pour l'utilisateur
    //     // Par exemple, cela pourrait être basé sur la dernière entrée dans une liste de services
    //     $services = $user->getServices(); // Assurez-vous que cette méthode existe et renvoie les services
    //     $lastService = $services->last(); // Récupère le dernier service de la collection
    //     return $lastService ? $lastService->getName() : null;
    // }

}
