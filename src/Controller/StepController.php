<?php

namespace App\Controller;

use App\Repository\StepRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StepController extends AbstractController
{
    #[Route('/api/steps', name: 'app_steps_list', methods: ['GET'])]
    public function index(StepRepository $stepRepository): JsonResponse
    {
        $steps = $stepRepository->findAll();
        $data = [];

        foreach ($steps as $step) {
            $data[] = [
                'id' => $step->getId(),
                'position' => $step->getPosition(),
                'title' => $step->getTitle(),
                'isUnlocked' => $step->isUnlocked(),
            ];
        }

        return new JsonResponse($data);
    }
}