<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Facture;
use App\Entity\FactureLigne;
use App\Entity\Contact;
use App\Entity\Devis;
use App\Entity\DevisLigne;
use App\Entity\DevisVersion;
use App\Entity\DocumentsUtilisateur;
use App\Entity\Dossier;
use App\Entity\Events;
use App\Entity\Image;
use App\Entity\Paiement;
use App\Entity\Repertoire;
use App\Entity\Services;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class DashboardController extends AbstractDashboardController
{   
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    #[Route('/admin', name: 'admin')]
    public function home_index():Response
    {
        $url = $this->cache->get('admin_url', function (ItemInterface $item) {
            $item->expiresAfter(3600); 
            $routeBuilder = $this->container->get(AdminUrlGenerator::class);
            return $routeBuilder->setController(UserCrudController::class)->generateUrl();
        });

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tableaud d\'administration');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Retour vers le site', 'fa fa-home', '/');

        // Section Utilisateurs
        yield MenuItem::section('Gestion des utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class);
        
        // Section Facturation
        yield MenuItem::section('Gestion de la facturation');
        yield MenuItem::linkToCrud('Factures', 'fa fa-file-invoice-dollar', Facture::class);
        yield MenuItem::linkToCrud('Lignes Factures', 'fa fa-file-invoice-dollar', FactureLigne::class);
        yield MenuItem::linkToCrud('Paiements', 'fa fa-credit-card', Paiement::class);
        
        // Section Devis et Documents
        yield MenuItem::section('Gestion des Devis');
        yield MenuItem::linkToCrud('Devis', 'fa fa-file-contract', Devis::class);
        yield MenuItem::linkToCrud('Lignes Devis', 'fa fa-file-contract', DevisLigne::class);
        yield MenuItem::linkToCrud('Versions de devis', 'fa fa-copy', DevisVersion::class);
        
        // Section Répertoire et Services
        yield MenuItem::section('Services');
        yield MenuItem::linkToCrud('Services', 'fa fa-tools', Services::class);
 
        yield MenuItem::section('Events et Dossiers');
        yield MenuItem::linkToCrud('Dossier', 'fa fa-file-alt', Dossier::class);
        yield MenuItem::linkToCrud('Événements', 'fa fa-calendar', Events::class);

        yield MenuItem::section('Documents et Répertoire');
        yield MenuItem::linkToCrud('Répertoire', 'fa fa-folder', Repertoire::class);
        yield MenuItem::linkToCrud('Documents', 'fa fa-file-alt', DocumentsUtilisateur::class);
        
        yield MenuItem::section('Contact et Images');
        yield MenuItem::linkToCrud('Contacts', 'fa fa-users', Contact::class);
        yield MenuItem::linkToCrud('Images', 'fa fa-file-contract', Image::class);
    }
}    
