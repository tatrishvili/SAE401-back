<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Badge;
use App\Entity\User;
use App\Service\GamificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserStatsController extends AbstractController
{
    #[Route('/api/me/stats', name: 'api_me_stats', methods: ['GET'])]
    public function stats(): JsonResponse
    {
        $user = $this->getAuthenticatedUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Utilisateur non authentifié.'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json($this->normalizeUserStats($user));
    }

    #[Route('/api/debug/add-xp', name: 'api_debug_add_xp', methods: ['POST'])]
    public function addXp(Request $request, GamificationService $gamificationService): JsonResponse
    {
        $user = $this->getAuthenticatedUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Utilisateur non authentifié.'], Response::HTTP_UNAUTHORIZED);
        }

        $payload = [];

        try {
            $decodedPayload = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            if (is_array($decodedPayload)) {
                $payload = $decodedPayload;
            }
        } catch (\JsonException) {
            $payload = [];
        }

        $amount = $this->extractAmount($payload, $request);
        $unlockedBadges = $gamificationService->addXp($user, $amount);

        return $this->json([
            'message' => 'XP ajoutée avec succès.',
            'addedXp' => $amount,
            'stats' => $this->normalizeUserStats($user),
            'unlockedBadges' => array_map(
                static fn (Badge $badge): array => [
                    'id' => $badge->getId(),
                    'name' => $badge->getName(),
                    'description' => $badge->getDescription(),
                    'imageUrl' => $badge->getImageUrl(),
                    'xpThreshold' => $badge->getXpThreshold(),
                ],
                $unlockedBadges,
            ),
        ]);
    }

    private function getAuthenticatedUser(): ?User
    {
        $user = $this->getUser();

        return $user instanceof User ? $user : null;
    }

    private function normalizeUserStats(User $user): array
    {
        $badges = [];

        foreach ($user->getBadges() as $badge) {
            if (!$badge instanceof Badge) {
                continue;
            }

            $badges[] = $this->normalizeBadge($badge);
        }

        return [
            'xp' => $user->getXp(),
            'badges' => $badges,
        ];
    }

    private function normalizeBadge(Badge $badge): array
    {
        return [
            'id' => $badge->getId(),
            'name' => $badge->getName(),
            'description' => $badge->getDescription(),
            'imageUrl' => $badge->getImageUrl(),
            'xpThreshold' => $badge->getXpThreshold(),
        ];
    }

    private function extractAmount(array $payload, Request $request): int
    {
        $amount = $payload['amount'] ?? $request->query->get('amount', 10);

        if (is_string($amount) && is_numeric($amount)) {
            $amount = (int) $amount;
        }

        if (!is_int($amount)) {
            $amount = 10;
        }

        return max(0, $amount);
    }
}