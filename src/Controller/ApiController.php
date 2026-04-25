<?php
namespace App\Controller;

use App\Service\ImpactCo2ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}