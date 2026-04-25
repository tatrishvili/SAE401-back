<?php
namespace App\Controller;

use App\Service\ImpactCo2ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        $km = $request->query->get('km');
        $transports = $request->query->get('transports');
        $data = $this->impactService->getCategoryData('transport', [
            'km' => $km,
            'transports' => $transports,
        ]);
        return $this->json(['data' => $data]);
    }

    #[Route('/api/food', name: 'api_food', methods: ['GET'])]
    public function food(Request $request): JsonResponse
    {
        $category = $request->query->get('category', '');
        $data = $this->impactService->getCategoryData('food', [
            'category' => $category,
        ]);
        return $this->json(['data' => $data]);
    }

    #[Route('/api/fruitsetlegumes', name: 'api_fruitsetlegumes', methods: ['GET'])]
    public function fruitsEtLegumes(Request $request): JsonResponse
    {
        $month = $request->query->get('month', date('n'));
        $data = $this->impactService->getCategoryData('fruits-and-veggies', [
            'month' => $month,
        ]);
        return $this->json(['data' => $data]);
    }
}
