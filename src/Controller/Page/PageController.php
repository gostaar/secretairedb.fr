<?php

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{

    /**
     * Definition of sections
     */
    private function getSections()
    {
        $sections = [
            'pro' => [
                'template' => 'partials/Page/index.html.twig',
                'subFragments' => [
                    'acceuil' => 'partials/Page/_intro.html.twig',
                    'tarifs' => 'partials/Page/_tarif.html.twig',
                    'service' => 'partials/Page/_service.html.twig',
                    'contact' => 'partials/Page/_contact.html.twig',
                ]
            ],
            'part' => [
                'template' => 'partials/Page/index.html.twig',
                'subFragments' => [
                    'acceuil' => 'partials/Page/_intro.html.twig',
                    'tarifs' => 'partials/Page/_tarif.html.twig',
                    'service' => 'partials/Page/_service.html.twig',
                    'contact' => 'partials/Page/_contact.html.twig',
                    'job' => 'partials/Page/_job.html.twig',
                ],
            ],
        ];

        return ['sections' => $sections];
    }

    /**
     * Variables of initialization
     */
    private function getInitializations($fragment)
    {
        return [
            'fragmentValue' => $fragment,
            'fragmentButtonValue' => $fragment === 'part' ? 'pro' : 'part',
            'buttonValue' => $fragment === 'part' ? 'Vers les pros' : 'Vers les particuliers',
            'titleAdministratif' => $fragment === 'part' ? 'Gestion Administrative' : 'Services Administratifs',
            'titleNumerique' => $fragment === 'part' ? 'Gestion Numérique' : 'Services de Développement Numérique',
        ];
    }

    #[Route('/', name: 'home_index')]
    public function index(Request $request): Response
    {   
        return $this->render('pages/index.html.twig');
    }


    #[Route('/site/{fragment}/{subfragment}', name: 'subfragment')]
    public function subfragment(Request $request): Response
    {
        $fragment = $request->attributes->get('fragment');
        $subfragment = $request->attributes->get('subfragment');

        $initialisation = $this->getInitializations($fragment);

        $data = $this->getSections();
        $sections = $data['sections'];

        $fragmentTemplate = $sections[$fragment];
        $subFragmentTemplate = $sections[$fragment]['subFragments'][$subfragment];

        return $this->render('partials/Page/index.html.twig', [
            'subfragmentTemplate' => $subFragmentTemplate,
            'initialisation' => $initialisation,
        ]);
    }
}
