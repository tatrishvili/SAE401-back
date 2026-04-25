<?php

namespace App\Controller;

use App\Repository\TipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TipController extends AbstractController
{
    #[Route('/api/tips', name: 'api_tips_list', methods: ['GET'])]
    public function index(TipRepository $tipRepository): JsonResponse
    {
        $tips = $tipRepository->findAll();

        $data = array_map(static fn ($tip) => [
            'id' => $tip->getId(),
            'title' => $tip->getTitle(),
            'text' => $tip->getText(),
            'tag' => $tip->getTag(),
        ], $tips);

        return new JsonResponse($data);
    }
}
