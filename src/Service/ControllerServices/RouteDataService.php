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
        $config = $routeConfig[$fragment] ?? $this->createServiceConfig("");

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
            'link-Administratif' => $this->createServiceConfig('Administratif'),
            'link-Agenda' => $this->createServiceConfig('Agenda'),
            'link-Commercial' => $this->createServiceConfig('Commercial'),
            'link-Numerique' => $this->createServiceConfig('Numerique'),
            'link-Telephone' => $this->createServiceConfig('Telephonique'),
            'link-MainAgenda' => $this->createServiceConfig('Agenda'),
            'link-PageAgenda' => $this->createServiceConfig('Agenda'),
            '' => $this->createServiceConfig('Acceuil'),
            'link-Acceuil' => $this->createServiceConfig('Acceuil'),
            'link-Repertoire' => $this->createServiceConfig('Repertoire'),
            // 'link-Acceuil' => $this->createServiceConfig('Repertoire'),
            // 'link-espacepersonnel' => $this->createServiceConfig('Repertoire'),
            // 'link-Profile' => $this->createServiceConfig('Repertoire'), 
            // 'link-Factures' => $this->createServiceConfig('Repertoire'),
            // 'link-Devis' => $this->createServiceConfig('Repertoire'),
            // 'link-Repertoire' => $this->createServiceConfig('Repertoire'),
            // 'link-parametres' => $this->createServiceConfig('Repertoire'),
            // 'link-PageRepertoire' => $this->createServiceConfig('Repertoire'),
            // 'link-PageFacture' => $this->createServiceConfig('Repertoire'),
            // 'link-PageDevis' => $this->createServiceConfig('Repertoire'),
        ];
    }

    public function getStaticData($user, $fragment)
    {
        return $staticData = [
            'addContact' => \App\Form\ContactType::class,
            'addDevis' => \App\Form\DevisType::class,
            'addDevisLigne' => \App\Form\DevisLigneType::class,
            'addDevisVersion' => \App\Form\DevisVersionType::class,
            'addDocument' => \App\Form\DocumentsUtilisateurType::class,
            'addAddDocument' => \App\Form\AddDocumentsUtilisateurType::class,
            'addImage' => \App\Form\ImageType::class,
            'addAddImage' => \App\Form\AddImageType::class,
            'addDossier' => \App\Form\DossierType::class,
            'addEvents' => \App\Form\EventsType::class,
            'addFactureLigne' => \App\Form\FactureLigneType::class,
            'addFacture' => \App\Form\FactureType::class,
            'addRepertoire' => \App\Form\RepertoireType::class,
            'addServices' => \App\Form\ServicesType::class,
            'addTypeDocument' => \App\Form\TypeDocumentType::class,
            'addUserForm' => \App\Form\UserType::class, 
            'addSearchData' => \App\Form\SearchType::class,
            'addEditPassword' => \App\Form\ChangePasswordType::class,
            'addIdentifiants' => \App\Form\IdentifiantsType::class,
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
            'addAddDocument' => \App\Entity\DocumentsUtilisateur::class,
            'addImage' => \App\Entity\Image::class,
            'addAddImage' => \App\Entity\Image::class,
            'addDossier' => \App\Entity\Dossier::class,
            'addEvents' => \App\Entity\Events::class,
            'addFactureLigne' => \App\Entity\FactureLigne::class,
            'addFacture' => \App\Entity\Facture::class,
            'addRepertoire' => \App\Entity\Repertoire::class,
            'addServices' => \App\Entity\Services::class,
            'addTypeDocument' => \App\Entity\TypeDocument::class,
            'addUserForm' => \App\Entity\User::class,
            'addSearchData' => \App\Model\SearchData::class,
            'addEditPassword' => \App\Model\ChangePasswordModel::class,
            'addIdentifiants' => \App\Entity\Identifiants::class,
        ];
    
        return $formEntityClasses[$key] ?? \App\Entity\User::class;
    }

    public function getTemplateMapping(): array
    {
        return [
            'link-Administratif' => 'partials/user/Dossiers/index.html.twig',
            'link-Agenda' => 'partials/user/Agenda/_agenda.html.twig',
            'link-Commercial' => 'partials/user/Dossiers/index.html.twig',
            'link-Factures' => 'partials/user/EspacePersonnel/FactureDevis/factures.html.twig',
            'link-Devis' => 'partials/user/EspacePersonnel/FactureDevis/devis.html.twig',
            'link-MainAgenda' => 'partials/user/Agenda/_agendaMain.html.twig',
            'link-Numerique' => 'partials/user/Dossiers/index.html.twig',
            'link-PageAgenda' => 'partials/user/Agenda/index.html.twig',
            'link-PageRepertoire' => 'partials/user/Repertoire/repertoire.html.twig',
            'link-Repertoire' => 'partials/user/Dossiers/index.html.twig',
            'link-RepertoireEdit' => 'partials/user/Repertoire/_repertoireEdit.html.twig',
            'link-Telephone' => 'partials/user/Dossiers/index.html.twig',

            // 'link-Acceuil' => 'userPage/_mainContent.html.twig',
            'link-espacepersonnel' => 'partials/user/EspacePersonnel/index.html.twig',
            'link-DocumentEdit' => 'partials/user/_documentEdit.html.twig',
            'link-newDocument' => 'partials/user/Administratif/_newDocuments.html.twig',
            'link-PageDocument' => 'partials/user/document.html.twig',
            'link-PageDossier' => 'partials/user/dossier.html.twig',
            'link-parametres' => 'partials/user/EspacePersonnel/parametres.html.twig',
            'link-Profile' => 'partials/user/EspacePersonnel/profile.html.twig',
            'offresPartenaires' => 'userPage/_offresPartenaires.html.twig',
            'rgpd' => 'userPage/_rgpd.html.twig',
            's' => 'userPage/_s.html.twig',
            
        ];
    }

    public function getFragmentMapping(string $key) : string
    {
        $fragmentMappging = [
            'link-Agenda' => 'Agenda',
            'link-MainAgenda' => 'Agenda',
            'link-PageAgenda' => 'Agenda',
            'link-Administratif' => 'Administratif',
            'link-Commercial' => 'Commercial',
            'link-Numerique' => 'Numerique',
            'link-Telephone' => 'Telephonique',
            // 'link-Repertoire' => 'Repertoire',
            // 'link-PageRepertoire' => 'Repertoire',
        ];
        return $fragmentMappging[$key] ?? 'Repertoire';
    }

    public function getFragmentEntityMapping(?string $key)
    {
        $commonEntities = [\App\Entity\User::class, \App\Entity\Services::class];
        $fragmentMappging = [
            'link-Acceuil' => $commonEntities,
            'link-Profile' => array_merge($commonEntities, [\App\Entity\Identifiants::class]),
            'link-Factures' => array_merge($commonEntities, [\App\Entity\Facture::class, \App\Entity\FactureLigne::class]),
            'link-Devis' => array_merge($commonEntities, [\App\Entity\Devis::class, \App\Entity\DevisLigne::class]),
            'link-espacepersonnel' => array_merge($commonEntities, [\App\Entity\Dossier::class]),
            'link-parametres' => array_merge($commonEntities, [\App\Model\ChangePasswordModel::class]),
            'link-Administratif' => array_merge($commonEntities, [\App\Entity\Dossier::class, \App\Model\SearchData::class ]),
            'link-PageDocument' => array_merge($commonEntities, [\App\Entity\DocumentsUtilisateur::class, \App\Entity\Image::class, \App\Model\SearchData::class]),
            'link-DocumentEdit' => array_merge($commonEntities, [\App\Entity\DocumentsUtilisateur::class, \App\Entity\Image::class]),
            'link-Agenda' => array_merge($commonEntities, [\App\Entity\Events::class]),
            'link-Commercial' => array_merge($commonEntities, [\App\Entity\Dossier::class, \App\Model\SearchData::class]),
            'link-Telephone' => array_merge($commonEntities, [\App\Entity\Dossier::class, \App\Model\SearchData::class]),
            'link-Numerique' => array_merge($commonEntities, [\App\Entity\Dossier::class, \App\Model\SearchData::class]),
            'link-Repertoire' => array_merge($commonEntities, [\App\Entity\Dossier::class, \App\Model\SearchData::class]),
            'link-PageRepertoire' => array_merge($commonEntities, [\App\Entity\Repertoire::class, \App\Entity\Contact::class, \App\Model\SearchData::class]),
            'link-RepertoireEdit' => array_merge($commonEntities, [\App\Entity\Repertoire::class, \App\Entity\Contact::class]),
            
            // 'link-MainAgenda' => array_merge($commonEntities, [\App\Entity\Events::class]),
            // 'link-PageAgenda' => array_merge($commonEntities, [\App\Entity\Events::class]),
            // 'link-PageFacture' => array_merge($commonEntities, [\App\Entity\Facture::class, \App\Entity\FactureLigne::class]),
            // 'link-newDocument' => array_merge($commonEntities, [\App\Entity\DocumentsUtilisateur::class, \App\Entity\Image::class]),
            // 'link-PageDossier' => array_merge($commonEntities, [\App\Entity\Dossier::class]),
        ];
    
        return $fragmentMappging[$key] ?? $commonEntities;
    }
    
    public function createServiceConfig(string $service): array
    {
        return ['serviceLink' => $service];
    }

}
