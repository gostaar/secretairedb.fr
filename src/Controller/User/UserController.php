<?php

namespace App\Controller\User;

use App\Service\ControllerServices\FragmentDataService;
use App\Service\ControllerServices\FormService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private FragmentDataService $fragmentDataService;
    private FormService $formService;  
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormService $formService,
        FragmentDataService $fragmentDataService,
        UserPasswordHasherInterface $passwordHasher,
    ){
        $this->entityManager = $entityManager;
        $this->fragmentDataService = $fragmentDataService;
        $this->formService = $formService;
        $this->passwordHasher = $passwordHasher;
    }

    public function main(?int $dossierId, ?int $documentId, ?int $repertoireId, ?string $service)
    {
        $user = $this->getUser();  

        $formFragment = $this->fragmentDataService->getFragmentData($dossierId, $user, $documentId, $repertoireId, $service);

        return $formFragment;
    }

    #[Route('/user', name: 'user')]
    public function index(Request $request): Response
    {
        $formData = $this->main(null, null, null, null);

        return $this->render('userPage/user.html.twig', [
            'user' => $formData['user'],
            'services' => $formData['services']
        ]);
    }

    #[Route('/user/profile', name: 'profile')]
    public function profile(Request $request): Response
    {
        $formData = $this->main(null, null, null, null);

        $user = $this->entityManager->getRepository(\App\Entity\User::class)->find($this->getUser()->getId());

        $formUser = $this->createForm(\App\Form\UserType::class, $user);
        $formUser->handleRequest($request);
        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $user->setNom($user->getNom());
            $user->setAdresse($user->getAdresse());
            $user->setCodePostal($user->getCodePostal());
            $user->setVille($user->getVille());
            $user->setPays($user->getPays());
            $user->setTelephone($user->getTelephone());
            $user->setMobile($user->getMobile());
            $user->setEmail($user->getEmail());
            $user->setSiret($user->getSiret());
            $user->setNomEntreprise($user->getNomEntreprise());
            $this->entityManager->flush();
            return $this->redirect("/user/profile");
        }

        $identifiants = new \App\Entity\Identifiants();
        $formIdentifiants = $this->createForm(\App\Form\IdentifiantsType::class, $identifiants);
        $formIdentifiants->handleRequest($request);
        if ($formIdentifiants->isSubmitted() && $formIdentifiants->isValid()) {
            $user->addIdentifiant($identifiants);
            $this->entityManager->flush();
            return $this->redirect("/user/profile");
        }

        return $this->render('partials/user/EspacePersonnel/profile.html.twig', [
            'formUser' => $formUser,
            'formIdentifiants' => $formIdentifiants,
            'user' => $formData['user'],
            'services' => $formData['services'],
            'identifiants' => $formData['identifiants'],
        ]);
    }

    #[Route('/user/espacePersonnel', name: 'espacePersonnel')]
    public function espacePersonnel(Request $request): Response
    {
        $formData = $this->main(null, null, null, null);
        return $this->render('partials/user/EspacePersonnel/index.html.twig', [
            'user' => $formData['user'],
            'services' => $formData['services'],
            'factures' => $formData['factures'],
            'devis' => $formData['devis']
        ]);
    }

    #[Route('/user/factures', name: 'factures')]
    public function factures(): Response
    {
        $formData = $this->main(null, null, null, null);

        return $this->render('partials/user/EspacePersonnel/FactureDevis/factures.html.twig', [
            'user' => $formData['user'],
            'services' => $formData['services'],
            'factures' => $formData['factures'],
        ]);
    }

    #[Route('/user/devis', name: 'devis')]
    public function devis(): Response
    {
        $formData = $this->main(null, null, null, null);
        return $this->render('partials/user/EspacePersonnel/FactureDevis/devis.html.twig', [
            'user' => $formData['user'],
            'services' => $formData['services'],
            'devis' => $formData['devis'],
        ]);
    }

    #[Route('/user/parametres', name: 'parametres')]
    public function parametres(Request $request): Response
    {
        $formData = $this->main(null, null, null, null);

        $user = $this->entityManager->getRepository(\App\Entity\User::class)->find($this->getUser()->getId());

        $password = new \App\Model\ChangePasswordModel();
        $form = $this->createForm(\App\Form\ChangePasswordType::class, $password);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Votre mot de passe actuel est incorrect.');
            }

            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            }

            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $this->entityManager->getRepository(\App\Entity\User::class)->updatePassword($user, $hashedPassword);

            $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');
            $this->entityManager->flush();
            return $this->redirect("/user/parametres");
        }

        return $this->render('partials/user/EspacePersonnel/parametres.html.twig', [
            'form' => $form,
            'user' => $formData['user'],
            'services' => $formData['services'],
            'devis' => $formData['devis'],
        ]);
    }
    
    #[Route('/user/offresPartenaires', name: 'offresPartenaires')]
    public function offresPartenaires()
    {
        $formData = $this->main(null, null, null, null);
        return $this->render('userPage/_offresPartenaires.html.twig', [
            'services' => $formData['services'],
        ]);
    }

    #[Route('/user/rgpd', name: 'rgpd')]
    public function rgpd()
    {
        $formData = $this->main(null, null, null, null);
        return $this->render('userPage/_RGPD.html.twig', [
            'services' => $formData['services'],
        ]);
    }

    #[Route('/user/_s', name: '_s')]
    public function _s()
    {
        $formData = $this->main(null, null, null, null);
        return $this->render('userPage/_s.html.twig', [
            'services' => $formData['services'],
        ]);
    }

    #[Route('/user/{service}', name: 'dossiers')]
    public function dossiers(Request $request): Response
    {
        $service = $request->attributes->get('service');
        $formData = $this->main(null, null, null, $service);

        if($service === "Agenda"){
            $googleToken = $this->entityManager->getRepository(\App\Entity\GoogleToken::class)->findOneBy([]);
            
            if($formData['isTokenValid']){
                return $this->render('partials/user/Agenda/_agendaMain.html.twig', [
                    'events' => $formData['events'],
                    'services' => $formData['services'],
                    'service' => $service,
                    'origin' => 'Google',
                ]);              
            }

            return $this->render('partials/user/Agenda/_agenda.html.twig', [
                'services' => $formData['services'],
                'service' => $service,
                'events' => $formData['events'],
            ]);
        }
        
        $dossier = new \App\Entity\Dossier();
        $formDossier = $this->createForm(\App\Form\AddDossierType::class, $dossier, [
            'userId' => $this->getUser()->getId(),
            'serviceId' => $this->entityManager->getRepository(\App\Entity\Services::class)->getServiceByName($service),
        ]);
        $formDossier->handleRequest($request);
        if ($formDossier->isSubmitted() && $formDossier->isValid()) {
            $this->entityManager->persist($dossier);
            $this->entityManager->flush();
            return $this->redirect("/user/{$service}");
        }
        
        $search = new \App\Model\SearchData();
        $formSearch = $this->createForm(\App\Form\SearchType::class, $search);
        $formSearch->handleRequest($request);
        $dossiers = null;
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $dossiers = $this->formService->searchInFile($search, $this->getUser(), $service);
        }

        return $this->render('partials/user/Dossiers/index.html.twig', [
            'formDossier' => $formDossier,
            'formSearch' => $formSearch,
            'dossiers' => $dossiers !== null ? $dossiers : $formData['dossiers'],
            'dossier' => $formData['dossier'],
            'services' => $formData['services'],
            'service' => $service,
            'documents' => $formData['documents']
        ]);
    }

    #[Route('/user/events/{service}', name: 'event')]
    public function eventId(Request $request): Response
    {
        $service = $request->attributes->get('service');
        $serviceEntity = $this->entityManager->getRepository(\App\Entity\Services::class)->getServiceByName($service);
        $formData = $this->main(null, null, null, $service);
        $origin = $request->query->get('origin', 'User');

        $event = new \App\Entity\Events();
        $formEvent = $this->createForm(\App\Form\EventsType::class, $event);

        $formEvent->handleRequest($request);
        if ($formEvent->isSubmitted() && $formEvent->isValid()) {
            $event->setUser($this->getUser());
            $event->setServices($serviceEntity);
            $this->entityManager->persist($event);
            $this->entityManager->flush();
            return $this->redirect("/user/events/{$service}");
        }
        
        $googleToken = $this->entityManager->getRepository(\App\Entity\GoogleToken::class)->findBy(['users' => $this->getUser()->getId()]);
  
        if(isset($googleToken) && $origin === "Google"){
            $events = $this->fragmentDataService->fetchGoogleCalendarEvents($googleToken[0]->getAccessToken());
        } else {
             $events = [];
            foreach ($formData['events'] as $event) {
                $events[] = [
                    'title' => $event->getTitle(),
                    'start' => $event->getStart()->format('Y-m-d H:i:s'),
                    'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                    'location' => $event->getLocation(),
                    'description' => $event->getDescription(),
                ];
            }    
        }

        return $this->render('partials/user/Agenda/_agendaMain.html.twig', [
            'formEvent' => $formEvent,
            'events' => $events,
            'services' => $formData['services'],
            'service' => $service,
            'origin' => $origin,
        ]);
    }

    #[Route('/user/{service}/{dossierId}', name: 'dossier')]
    public function dossier(Request $request): Response
    {
        $service = $request->attributes->get('service');
        $dossierId = $request->attributes->get('dossierId');
        
        $search = new \App\Model\SearchData();
        $formSearch = $this->createForm(\App\Form\SearchType::class, $search);
        $formSearch->handleRequest($request);
        $documents = null;
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            if($service === "Repertoire"){
                $documents = $this->formService->searchInRepository($search, $this->getUser(), $dossierId);
            } else {
                $documents = $this->formService->searchInDocument($search, $this->getUser(), $dossierId);
            }
        }

        $formData = $this->main($dossierId, null, null, $service);

        if($service === "Repertoire"){
            $repertoire =  new \App\Entity\Repertoire();
            $formRepertoire = $this->createForm(\App\Form\AddRepertoireType::class, $repertoire, [
                'userId' => $this->getUser()->getId(),
                'dossierId' => $dossierId,
            ]);
            $formRepertoire->handleRequest($request);
            if ($formRepertoire->isSubmitted() && $formRepertoire->isValid()) {
                $this->entityManager->persist($repertoire);
                $this->entityManager->flush();
                return $this->redirect("/user/{$service}/{$dossierId}");
            }
            
            return $this->render('partials/user/Repertoire/repertoire.html.twig', [
                'formRepertoire' => $formRepertoire,
                'formSearch' => $formSearch,
                'dossier' => $formData['dossier'],
                'dossiers' => $formData['dossiers'],
                'services' => $formData['services'],
                'service' => $service,
                'repertoires' => $documents !== null ? $documents : $formData['repertoires']
            ]);
        } else {
            $document =  new \App\Entity\DocumentsUtilisateur();
            $formDocument = $this->createForm(\App\Form\AddDocumentsUtilisateurType::class, $document, [
                'userId' => $this->getUser()->getId(),
                'documentId' => $documentId ?? null,
                'dossierId' => $dossierId
            ]);
            $formDocument->handleRequest($request);
            if ($formDocument->isSubmitted() && $formDocument->isValid()) {
                $type = $request->request->get('type');
                $addDoc = $request->request->all('add_documents_utilisateur')['name'];
                $document->setName($addDoc . $type);
                $this->entityManager->persist($document);
                $this->entityManager->flush();
                return $this->redirect("/user/{$service}/{$dossierId}");
            }

            return $this->render('partials/user/document.html.twig', [
                'formDocument' => $formDocument,
                'formSearch' => $formSearch,
                'dossiers' => $formData['dossiers'],
                'dossier' => $formData['dossier'],
                'services' => $formData['services'],
                'service' => $service,
                'documents' => $documents !== null ? $documents : $formData['documents'],
            ]);
        }
    }

    #[Route('/user/{service}/{dossierId}/{documentId}', name: 'document')]
    public function document(Request $request): Response
    {
        $service = $request->attributes->get('service');
        $dossierId = $request->attributes->get('dossierId');
        $documentId = $request->attributes->get('documentId');

        if($service === "Repertoire"){
            $formData = $this->main($dossierId, null, $documentId, $service);

            $repertoireEntity = $this->entityManager->getRepository(\App\Entity\Repertoire::class)->find($documentId);

            $repertoire = $repertoireEntity;
            $formRepertoire = $this->createForm(\App\Form\RepertoireType::class, $repertoire, [
                'userId' => $this->getUser()->getId(),
                'dossierId' => $dossierId,
            ]);
            $formRepertoire->handleRequest($request);
            if ($formRepertoire->isSubmitted() && $formRepertoire->isValid()) {
                $this->entityManager->persist($repertoire);
                $this->entityManager->flush();
                return $this->redirect("/user/{$service}/{$dossierId}/{$documentId}");
            }

            $contact = new \App\Entity\Contact();
            $formContact = $this->createForm(\App\Form\ContactType::class, $contact);
            $formContact->handleRequest($request);
            if ($formContact->isSubmitted() && $formContact->isValid()) {
                $this->entityManager->persist($contact);
                $this->entityManager->flush();
                return $this->redirect("/user/{$service}/{$dossierId}/{$documentId}");
            }

            return $this->render('partials/user/Repertoire/_repertoireEdit.html.twig', [
                'formRepertoire' => $formRepertoire,
                'formContact' => $formContact,
                'dossier' => $formData['dossier'],
                'repertoire' => $formData['repertoire'],
                'services' => $formData['services'],
                'service' => $service,
            ]);
        } else {
            $formData = $this->main($dossierId, $documentId, null, $service);

            $documentEntity = $this->entityManager->getRepository(\App\Entity\DocumentsUtilisateur::class)->find($documentId);

            $document = $documentEntity;
            $formDocument = $this->createForm(\App\Form\DocumentsUtilisateurType::class, $document, [
                'userId' => $this->getUser(),
                'documentId' => $documentId,
                'dossierId' => $dossierId,
            ]);
            $formDocument->handleRequest($request);
            if ($formDocument->isSubmitted() && $formDocument->isValid()) {
                $this->entityManager->persist($document);
                $this->entityManager->flush();
                return $this->redirect("/user/{$service}/{$dossierId}/{$documentId}");
            }

            $createImage = new \App\Entity\Image();
            $formCreateImage = $this->createForm(\App\Form\AddImageType::class, $createImage);
            $formCreateImage->handleRequest($request);
            if ($formCreateImage->isSubmitted() && $formCreateImage->isValid()) {
                $this->entityManager->persist($createImage);
                $this->entityManager->flush();
                return $this->redirect("/user/{$service}/{$dossierId}/{$documentId}");
            }

            $imageId = null;
            if(isset($request->request->All()['image'])){ 
                $imageId = $this->entityManager->getRepository(\App\Entity\Image::class)->find($request->request->All()['image']['id']);
            }
        
            $editImage = $imageId !== null ? $imageId : new \App\Entity\Image();
            $formEditImage = $this->createForm(\App\Form\ImageType::class, $editImage);
            $formEditImage->handleRequest($request);
            if ($formEditImage->isSubmitted() && $formEditImage->isValid()) {
                $this->entityManager->flush();
                return $this->redirect("/user/{$service}/{$dossierId}/{$documentId}");
            }

            return $this->render('partials/user/_documentEdit.html.twig', [
                'formDocument' => $formDocument,
                'formCreateImage' => $formCreateImage,
                'formEditImage' => $formEditImage,
                'dossiers' => $formData['dossiers'],
                'services' => $formData['services'],
                'service' => $service,
                'documents' => $formData['documents'],
                'document' => $formData['document']
            ]);
        }
    }

    #[Route('/download/{id}', name: "image_download")]
    public function download(\App\Entity\Image $image): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/documents/' . $image->getImageName();
        
        return $this->file($filePath);
    }
}