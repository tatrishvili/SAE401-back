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
            'transports' => $transports,
        ]);

        return $this->json(['data' => $data]);
    }

    /**
     * /api/food?category=group|rayon|popularity
     * Returns food items grouped by category.
     */
    #[Route('/api/food', name: 'api_food', methods: ['GET'])]
    public function food(Request $request): JsonResponse
    {
        $category = $request->query->get('category', 'group');

        $data = $this->impactService->getCategoryData('food', [
            'category' => $category,
        ]);

        return $this->json(['data' => $data]);
    }

    /**
     * /api/fruitsetlegumes?month=4&category=1,2
     * Returns seasonal fruits & veggies for a given month.
     * Month defaults to current month if not provided.
     */
    #[Route('/api/fruitsetlegumes', name: 'api_fruitsetlegumes', methods: ['GET'])]
    public function fruitsLegumes(Request $request): JsonResponse
    {
        $month    = $request->query->get('month', (int) date('n')); // current month by default
        $category = $request->query->get('category', '');           // e.g. "1,2" for fruits+légumes

        $params = ['month' => $month];
        if ($category !== '') {
            $params['category'] = $category;
        }

        $data = $this->impactService->getCategoryData('fruits-and-veggies', $params);

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
