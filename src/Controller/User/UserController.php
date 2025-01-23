<?php

namespace App\Controller\User;

use App\Service\ControllerServices\FragmentDataService;
use App\Service\ControllerServices\FormService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private FragmentDataService $fragmentDataService;
    private FormService $formService;

    public function __construct(FragmentDataService $fragmentDataService, FormService $formService)
    {
        $this->fragmentDataService = $fragmentDataService;
        $this->formService = $formService;
    }

    public function main(Request $request)
    {
        $user = $this->getUser();  
        $fragment = $request->query->get('fragment', 'link-Acceuil');
        $dossierId = $request->query->get('dossier');
        $documentId = $request->query->get('document');   

        $formFragment = $this->fragmentDataService->getFragmentData($request, $fragment, $dossierId, $user, $documentId);
        $formViews = $this->formService->createFormViews($request, $formFragment, $user, $dossierId, $fragment, $documentId); 
        
        $formData = array_merge($formFragment, $formViews);

        //Recherche
        if($formData['dossierSearchResults']){ $formData['dossierSearchResults'] === 'vide' ? $formData['dossiers'] = null : $formData['dossiers'] = $formData['dossierSearchResults'];}
        if($formData['repertoireSearchResults']){ $formData['repertoireSearchResults'] === 'vide' ? $formData['repertoires'] = null : $formData['repertoires'] = $formData['repertoireSearchResults'];}
        if($formData['documentSearchResults']){ $formData['documentSearchResults'] === 'vide' ? $formData['documents'] = null : $formData['documents'] = $formData['documentSearchResults'];}
        if($formData['ligneFactureSearchResults']){ $formData['ligneFactureSearchResults'] === 'vide' ? $formData['ligneFacture'] = null : $formData['ligneFacture'] = $formData['ligneFactureSearchResults'];}
        if($formData['ligneDevisSearchResults']){ $formData['ligneDevisSearchResults'] === 'vide' ? $formData['ligneDevis'] = null : $formData['ligneDevis'] = $formData['ligneDevisSearchResults'];}

        return $formData;
    }

    #[Route('/user', name: 'user')]
    public function index(Request $request): Response
    {
        $formData = $this->main($request);

        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/changefragment', name: 'changefragment')]
    public function changefragment(Request $request): Response
    {
        $formData = $this->main($request);

        return $this->json([
            'fragmentContent' => $this->renderView('userPage/_fragmentContent.html.twig', $formData),
        ]);
    }
}
