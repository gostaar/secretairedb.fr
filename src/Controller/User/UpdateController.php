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

class UpdateController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository, EntityManagerInterface $entityManager){
        $this->contactRepository = $contactRepository;
        $this->entityManager = $entityManager;
    }
    
    #[Route('update_contact/{service}/{dossierId}/{repertoireId}/{contactId}', name: 'update_contact')]
    public function updateContact(Request $request, EntityManagerInterface $em): Response
    {
        $service = $request->attributes->get('service');
        $dossier = $request->attributes->get('dossierId');
        $repertoire = $request->attributes->get('repertoireId');
        $contactId = $request->attributes->get('contactId');

        $contact = $this->entityManager->getRepository(\App\Entity\Contact::class)->find($contactId);

        $data = $request->request->all();
        
        if (isset($data['nom'])) { $contact->setNom($data['nom']); }
        if (isset($data['telephone'])) { $contact->setTelephone($data['telephone']); }
        if (isset($data['email'])) { $contact->setEmail($data['email']);}
        if (isset($data['role'])) { $contact->setRole($data['role']);}
        if (isset($data['commentaire'])) { $contact->setCommentaire($data['commentaire']);}
        
        $em->flush();

        return $this->redirect("/user/{$service}/{$dossier}/{$repertoire}");
    }
    
    #[Route('/updateIdentifiant/{id}', name: 'update_identifiant')]
    public function updateIdentifiants(\App\Entity\Identifiants $identifiant, Request $request, EntityManagerInterface $em) : Response
    {
        $data = $request->request->all();

        if (isset($data['site'])) { $identifiant->setSite($data['site']); }
        if (isset($data['identifiant'])) { $identifiant->setIdentifiant($data['identifiant']); }
        if (isset($data['password'])) { $identifiant->setPassword($data['password']); }

        $em->flush();

        return  $this->redirect('/user/profile');
    }
}