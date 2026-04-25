<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Badge;
use App\Entity\User;
use App\Repository\BadgeRepository;
use Doctrine\ORM\EntityManagerInterface;

class GamificationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BadgeRepository $badgeRepository,
    ) {
    }

    /**
     * @return list<Badge>
     */
    public function addXp(User $user, int $amount): array
    {
        $amount = max(0, $amount);
        $user->setXp($user->getXp() + $amount);

        $unlockedBadges = [];
        $badges = $this->badgeRepository->findBy([], ['xpThreshold' => 'ASC', 'id' => 'ASC']);

        foreach ($badges as $badge) {
            if ($user->getXp() < $badge->getXpThreshold()) {
                continue;
            }

            if ($user->getBadges()->contains($badge)) {
                continue;
            }

            $user->addBadge($badge);
            $unlockedBadges[] = $badge;
        }

        $this->entityManager->flush();

        return $unlockedBadges;
    }
}