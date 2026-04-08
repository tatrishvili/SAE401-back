<?php
// src/Security/DailyEntryVoter.php
namespace App\Security;

use App\Entity\DailyEntry;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DailyEntryVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['edit', 'delete']) && $subject instanceof DailyEntry;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var DailyEntry $entry */
        $entry = $subject;

        return $entry->getUser()->getId() === $user->getId();
    }
}
