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

    private ContactRepository $contactRepository;

    public function __construct(ContactRepository $contactRepository){
        $this->contactRepository = $contactRepository;
    }
    
    #[Route('{id}/update_contact/{repertoireId}', name: 'update_contact')]
    public function updateContact(\App\Entity\Contact $contact, $repertoireId, Request $request, EntityManagerInterface $em): Response
    {
        $url = $_ENV['URL'];
        
        $data = $request->request->all();
        
        if (isset($data['nom'])) { $contact->setNom($data['nom']); }
        if (isset($data['telephone'])) { $contact->setTelephone($data['telephone']); }
        if (isset($data['email'])) { $contact->setEmail($data['email']);}
        if (isset($data['role'])) { $contact->setRole($data['role']);}
        if (isset($data['commentaire'])) { $contact->setCommentaire($data['commentaire']);}
        
        $em->flush();

        return  $this->redirect($url . '/user?fragment=link-RepertoireEdit&repertoire=' . $repertoireId);
    }
    
    #[Route('{id}/update_image/{documentId}', name: 'update_image')]
    public function updateImage(\App\Entity\Image $image, $documentId, Request $request, EntityManagerInterface $em) : Response
    {
        $url = $_ENV['URL'];

        $data = $request->request->all();
        
        if (isset($data['slug'])) { $image->setSlug($data['slug']); }
        if (isset($data['imageName'])) { $image->setImageName($data['imageName']); }
        if (isset($data['imageSize'])) { $image->setImageSize($data['imageSize']);}
        if (isset($data['imageDescription'])) { $image->setImageDescription($data['imageDescription']);}
        
        $em->flush();

        return  $this->redirect($url . '/user?fragment=link-DocumentEdit&document=' . $documentId);
    }

    #[Route('/update_user/{id}', name: 'update_user')]
    public function updateUser(\App\Entity\USer $user)
    {
        
    }
}