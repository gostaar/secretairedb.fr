<?php

namespace App\Controller\User;
use App\Entity\Dossier;
use App\Form\DossierType;
use App\Service\DossierService;
use App\Entity\DocumentsUtilisateur;
use App\Form\DocumentsUtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DossierController extends AbstractController
{
    private EntityManagerInterface $em;
    private DossierService $dossierService;
    
    public function __construct(EntityManagerInterface $em, DossierService $dossierService){
        $this->em = $em;
        $this->dossierService = $dossierService;
    }

    public function new($serviceName, Request $request, SerializerInterface $serializer): Response
    {
        $dossier = new Dossier();
        $userId = $this->getUser()->getId();
        $user = $this->em->getRepository(\App\Entity\User::class)->find($userId);
        $data = $request->request->all()['dossier'];
        
        if (isset($data['name'])) { $dossier->setName($data['name']); }
        
        $service = $this->em->getRepository(\App\Entity\Services::class)->findOneBy(['name' => $serviceName]);
        $dossier->setUser($user);
        $dossier->setServices($service);

        $this->em->persist($dossier);
        $this->em->flush();

        return new Response('Dossier créé avec succès', Response::HTTP_OK);
    }

    public function edit($dossierId, Request $request) : Response 
    {
        $dossier = $this->em->getRepository(\App\Entity\Dossier::class)->find(intval($dossierId));
        //$userId = $this->getUser()->getId();
        //$user = $this->em->getRepository(\App\Entity\User::class)->find($userId);
        $data = $request->request->all();
        dd($data);
        if (isset($data['name'])) { $dossier->setName($data['name']); }
        
        $service = $this->em->getRepository(\App\Entity\Services::class)->findOneBy(['name' => $serviceName]);
        $dossier->setUser($user);
        $dossier->setServices($service);

        $this->em->persist($dossier);
        $this->em->flush();

        return new Response('Dossier créé avec succès', Response::HTTP_OK);
    }

    #[Route('/dossier/{id}', name: 'dossier', methods: ['GET', 'POST'])]
    public function getDossier($id)
    {
        $dossier = $this->dossierService->getDossier($id);

        $this->createForm(DocumentsUtilisateurType::class, new DocumentsUtilisateur(), [
            'dossier' => $dossier,
        ]);

        return $this->redirectToRoute('user', [
            'dossier' => $dossier,
        ], 302, ['fragment' => 'link-PageDossier']);
    }
        
    #[Route('/add_dossier', name: 'add_dossier', methods: ['POST'])]
    public function addDossier(Request $request): Response
    {
        $user = $this->getUser();
        $dossier = new Dossier();
        $dossierForm = $this->createForm(DossierType::class, $dossier);
        
        $dossierForm->handleRequest($request);
        
        if ($dossierForm->isSubmitted() && $dossierForm->isValid()) {
            $this->dossierService->addDossier($dossier, $user);
            
            $url = $this->generateUrl('user') . '?fragment=Repertoire';
            return new RedirectResponse($url);
        }
        return new Response();
    }

    #[Route('/update_dossier/{id}', name: 'update_dossier', methods: ['POST'])]
    public function updateDossier(int $id, Request $request) : JsonResponse
    {
        $name = $request->get('name');

        $user = $this->getUser();
        $this->dossierService->updateDossier($id, $user, $name);
        return new JsonResponse(['success' => true], 200);
    }
    
    #[Route('/delete_dossier/{id}', name: 'delete_dossier', methods: ['DELETE'])]
    public function deleteDossier(int $id) : JsonResponse
    {
        $user = $this->getUser();
        $this->dossierService->deleteDossier($id, $user);
        return new JsonResponse(['success' => true], 200);
    }

}