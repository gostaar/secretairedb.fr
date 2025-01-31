<?php

namespace App\Controller\User;

use App\Service\ControllerServices\FragmentDataService;
use App\Service\ControllerServices\FormService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ContactRepository;

class FormController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/profile', name: 'profile')]
    public function profile(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/factures', name: 'factures')]
    public function factures(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/facture/{id}', name: 'facture')]
    public function facture(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/devis', name: 'devis')]
    public function devis(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/devi/{id}', name: 'user')]
    public function devi(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/parametres', name: 'parametres')]
    public function parametres(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }
    
    #[Route('/user/commercial', name: 'commercial')]
    public function commercial(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}', name: 'commercialDossier')]
    public function commercialDossier(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}/{documentId}', name: 'commercialDocument')]
    public function commercialDocument(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial', name: 'commercial')]
    public function commercial(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}', name: 'commercialDossier')]
    public function commercialDossier(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}/{documentId}', name: 'commercialDocument')]
    public function commercialDocument(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial', name: 'commercial')]
    public function commercial(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}', name: 'commercialDossier')]
    public function commercialDossier(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}/{documentId}', name: 'commercialDocument')]
    public function commercialDocument(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial', name: 'commercial')]
    public function commercial(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}', name: 'commercialDossier')]
    public function commercialDossier(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}/{documentId}', name: 'commercialDocument')]
    public function commercialDocument(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial', name: 'commercial')]
    public function commercial(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}', name: 'commercialDossier')]
    public function commercialDossier(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}/{documentId}', name: 'commercialDocument')]
    public function commercialDocument(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial', name: 'commercial')]
    public function commercial(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}', name: 'commercialDossier')]
    public function commercialDossier(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}/{documentId}', name: 'commercialDocument')]
    public function commercialDocument(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial', name: 'commercial')]
    public function commercial(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}', name: 'commercialDossier')]
    public function commercialDossier(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

    #[Route('/user/commercial/{dossierId}/{documentId}', name: 'commercialDocument')]
    public function commercialDocument(Request $request): Response
    {
        $formData = $this->main($request);
        return $this->render('userPage/user.html.twig', $formData);
    }

}