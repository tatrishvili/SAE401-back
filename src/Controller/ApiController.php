<?php
// src/Controller/ApiController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ImpactCo2ApiService;

class ApiController extends AbstractController
{
    private ImpactCo2ApiService $impactService;

    public function __construct(ImpactCo2ApiService $impactService)
    {
        $this->impactService = $impactService;
    }

    #[Route('/api/categories', name: 'api_categories', methods: ['GET'])]
    public function categories(): JsonResponse
    {
        $data = $this->impactService->getCategories();
        return $this->json($data);
    }

    #[Route('/api/transport', name: 'api_transport', methods: ['GET'])]
    public function transport(Request $request): JsonResponse
    {
        $km         = $request->query->get('km', 100);
        $transports = $request->query->get('transports', '');

        $data = $this->impactService->getCategoryData('transport', [
            'km'         => $km,
            'transports' => $transports, // numeric ID(s), e.g. "2" for voiture thermique
        ]);

        return $this->json(['data' => $data]);
    }

    #[Route('/api/emission-factors/{category}', name: 'api_emission_factors', methods: ['GET'])]
    public function emissionFactors(string $category): JsonResponse
    {
        $query = [];
        if ($category === 'transport') {
            $query['km'] = 100;
        }
        if ($category === 'food') {
            $query['category'] = 'group';
        }

        $data = $this->impactService->getCategoryData($category, $query);
        return $this->json($data);
    }
}
