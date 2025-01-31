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
        $fragment = $request->query->get('fragment');
        $dossierId = $request->query->get('dossier');
        $documentId = $request->query->get('document');   
        $repertoireId = $request->query->get('repertoire');   

        $formFragment = $this->fragmentDataService->getFragmentData($request, $fragment, $dossierId, $user, $documentId, $repertoireId);
        $formViews = $this->formService->createFormViews($request, $formFragment, $user, $dossierId, $fragment, $documentId, $repertoireId); 
        
        $formData = array_merge($formFragment, $formViews);

        //Recherche
        if (isset($formData['searchResults']['dossierSearchResults'])){$formData['dossiers'] = $formData['searchResults']['dossierSearchResults'];}
        if (isset($formData['searchResults']['repertoireSearchResults'])){ $formData['repertoires'] = $formData['searchResults']['repertoireSearchResults'];}
        if (isset($formData['searchResults']['documentSearchResults'])){ $formData['documents'] = $formData['searchResults']['documentSearchResults'];}
        
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

        return $this->json(array_merge(
            ['fragmentContent' => $this->renderView('userPage/_fragmentContent.html.twig', $formData)],
            $formData
        ));
    }

}
