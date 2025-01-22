<?php

namespace App\Controller\Page;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\Service\ControllerServices\RedisService;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private RedisService $redisService;
    private CacheInterface $cache; 

    public function __construct(RedisService $redisService, CacheInterface $cache)
    {
        $this->redisService = $redisService;
        $this->cache = $cache;
    }

    /**
     * Definition of sections
     */
    private function getSections()
    {
        $commonFragments = [
            'acceuil' => 'partials/Page/_intro.html.twig',
            'tarifs' => 'partials/Page/_tarif.html.twig',
            'service' => 'partials/Page/_service.html.twig',
            'contact' => 'partials/Page/_contact.html.twig',
        ];

        $sections = [
            'pro' => [
                'template' => 'partials/Page/index.html.twig',
                'subFragments' => $commonFragments,
            ],
            'part' => [
                'template' => 'partials/Page/index.html.twig',
                'subFragments' => array_merge($commonFragments, [
                    'job' => 'partials/Page/_job.html.twig',
                ]),
            ],
            'partproChoice' => [
                'template' => 'pages/_partproChoice.html.twig',
            ],
        ];

        return [
            'sections' => $sections,
            'commonFragments' => $commonFragments,
        ];
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
        $session = $request->getSession();
        $session->invalidate();
        $response = new Response();
        $response->headers->clearCookie('PHPSESSID');

        $data = $this->getSections();
        $sections = $data['sections'];
        $commonFragments = $data['commonFragments'];

        $fragment = $request->query->get('fragment', 'partproChoice');
        
        $initialisation = $this->getInitializations($fragment);

        $subFragment = $request->query->get('subFragment', 'acceuil');

        $fragmentData = $sections[$fragment] ?? $sections['partproChoice'];
        $subFragmentTemplate = $fragmentData['subFragments'][$subFragment] ?? $commonFragments['acceuil'];

        $cacheKey = "subFragment_{$fragment}_{$subFragment}";
        $subFragmentContent = $this->cache->get($cacheKey, function (ItemInterface $item) use ($subFragmentTemplate, $initialisation) {
            $item->expiresAfter(3600);
            return $this->renderView($subFragmentTemplate, ['initialisation' => $initialisation]);
        });
        
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'fragmentContent' => $this->renderView(
                    $fragmentData['template'], 
                ),
                'subFragmentContent' => $subFragmentContent,
            ]);
        }   
       
        return $this->render('pages/index.html.twig', [
            'sections' => $sections,
            'currentFragment' => $fragment,
            'currentSubFragment' => $subFragment,
            'currentSubFragmentTemplate' => $subFragmentTemplate,
        ]);
    }
}
