<?php

namespace App\Controller;

use App\Repository\ChallengeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ChallengeController extends AbstractController
{
    #[Route('/api/challenges', name: 'app_challenges_list', methods: ['GET'])]
    public function index(ChallengeRepository $challengeRepository): JsonResponse
    {
        $challenges = $challengeRepository->findAll();
        $data = [];

        foreach ($challenges as $challenge) {
            $data[] = [
                'id' => $challenge->getId(),
                'title' => $challenge->getTitle(),
                'description' => $challenge->getDescription(),
                'category' => $challenge->getCategory(),
                'co2Reward' => $challenge->getCo2Reward(),
            ];
        }

        return new JsonResponse($data);
    }
}