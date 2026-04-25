<?php

namespace App\Controller;

use App\Entity\Step;
use App\Repository\StepRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StepController extends AbstractController
{
    #[Route('/api/steps', name: 'app_steps_list', methods: ['GET'])]
    public function index(StepRepository $stepRepository): JsonResponse
    {
        $steps = $stepRepository->findBy([], ['position' => 'ASC']);
        $data = [];
        foreach ($steps as $step) {
            $data[] = [
                'id' => $step->getId(),
                'position' => $step->getPosition(),
                'title' => $step->getTitle(),
                'isUnlocked' => $step->isUnlocked(),
                'isCompleted' => $step->isCompleted(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/api/steps/{id}/challenges', name: 'api_step_challenges', methods: ['GET'])]
    public function getChallenges(Step $step, StepRepository $stepRepo): JsonResponse
    {
        if (!$step->isCompleted()) {
            
            if ($step->getPosition() > 1) {
                $previousStep = $stepRepo->findOneBy(['position' => $step->getPosition() - 1]);
                
                if ($previousStep && $previousStep->getValidatedAt()) {
                    $diff = time() - $previousStep->getValidatedAt()->getTimestamp();
                    
                    if ($diff < 10) {
                        return $this->json(['error' => 'Attends demain !'], 403);
                    }
                }
            }
        }

        $challenges = $step->getChallenges();
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
        return $this->json($data);
    }

    #[Route('/api/steps/{id}/unlock-next', name: 'api_step_unlock_next', methods: ['POST'])]
    public function unlockNext(Step $currentStep, StepRepository $stepRepo, EntityManagerInterface $em): JsonResponse
    {
        $now = new \DateTimeImmutable();

        // On vérifie le temps écoulé depuis la dernière validation globale pour empêcher de tricher
        $lastCompletedStep = $stepRepo->findOneBy(
            ['isCompleted' => true],
            ['position' => 'DESC']
        );

        if ($lastCompletedStep && $lastCompletedStep->getValidatedAt()) {
            $diff = $now->getTimestamp() - $lastCompletedStep->getValidatedAt()->getTimestamp();
            
            if ($diff < 10) {
                $remaining = 10 - $diff;
                $hours = floor($remaining / 3600);
                $minutes = floor(($remaining % 3600) / 60);
                
                return $this->json([
                    'error' => "Patience ! Reviens dans environ $hours h $minutes min pour ton prochain défi."
                ], 403);
            }
        }

        $currentStep->setIsCompleted(true);
        $currentStep->setValidatedAt($now);

        $nextStep = $stepRepo->findOneBy(['position' => $currentStep->getPosition() + 1]);
        if ($nextStep) {
            $nextStep->setIsUnlocked(true);
        }

        $em->flush();

        return $this->json(['message' => 'Félicitations ! Journée validée.']);
    }
}