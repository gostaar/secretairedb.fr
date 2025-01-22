<?php
namespace App\Service\ControllerServices;

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

    public function getFormData(string $fragment): array
    {
        // Récupération de la configuration de la route
        $routeConfig = $this->getRouteConfig();
        $config = $routeConfig[$fragment] ?? $this->createServiceConfig('Repertoire');
        
        // Mapping des templates
        $templateMapping = $this->getTemplateMapping();
        $fragmentTemplate = $templateMapping[$fragment] ?? 'userPage/_mainContent.html.twig';
                
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

    public function getFragmentMapping(string $key) : string
    {
        $fragmentMappging = [
            // 'link-newDocument' => '',
            // 'link-PageDossier' => '',
            // 'link-PageDocument' => '',
            'link-Acceuil' => 'Repertoire',
            'link-Agenda' => 'Agenda',
            'link-MainAgenda' => 'Agenda',
            'link-PageAgenda' => 'Agenda',
            'link-Profile' => 'Repertoire',
            'link-espacepersonnel' => 'Repertoire',
            'link-Factures' => 'Repertoire',
            'link-PageFacture' => 'Repertoire',
            'link-parametres' => 'Repertoire',
            'link-Administratif' => 'Administratif',
            'link-Commercial' => 'Commercial',
            'link-Numerique' => 'Numerique',
            'link-Repertoire' => 'Repertoire',
            'link-Telephone' => 'Telephonique',
            'link-PageRepertoire' => 'Repertoire',
            'link-Devis' => 'Repertoire',
        ];
        return $fragmentMappging[$key] ?? 'Repertoire';
    }
    
    public function createServiceConfig(string $service): array
    {
        return ['service' => $service];
    }

}
