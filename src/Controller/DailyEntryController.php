<?php
// src/Controller/DailyEntryController.php
namespace App\Controller;

use App\Entity\DailyEntry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/entries')]
class DailyEntryController extends AbstractController
{
    #[Route('', name: 'api_entries_list', methods: ['GET'])]
    public function list(
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $entries = $em->getRepository(DailyEntry::class)->findBy(
            ['user' => $user],
            ['entryDate' => 'DESC']
        );

        $data = $serializer->serialize($entries, 'json', ['groups' => 'entry:read']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('', name: 'api_entries_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $data = json_decode($request->getContent(), true);

        // Validate required fields
        if (!isset($data['category']) || !isset($data['co2Value'])) {
            return $this->json(['error' => 'Missing required fields: category and co2Value'], 400);
        }

        // Parse the date
        $entryDate = new \DateTime($data['date'] ?? 'today');

        // Check if entry for this date + category already exists
        $existingEntry = $em->getRepository(DailyEntry::class)->findOneBy([
            'user' => $user,
            'entryDate' => $entryDate,
            'category' => $data['category']
        ]);

        if ($existingEntry) {
            // Update existing entry instead of creating duplicate
            $existingEntry->setCo2Value($existingEntry->getCo2Value() + $data['co2Value']);
            if (isset($data['details'])) {
                $existingEntry->setDetails(array_merge($existingEntry->getDetails(), $data['details']));
            }
            $em->flush();
            return $this->json($existingEntry, 200, [], ['groups' => 'entry:read']);
        }

        $entry = new DailyEntry();
        $entry->setUser($user);
        $entry->setEntryDate($entryDate);
        $entry->setCategory($data['category']);
        $entry->setCo2Value($data['co2Value']);
        $entry->setDetails($data['details'] ?? []);

        $em->persist($entry);
        $em->flush();

        return $this->json($entry, 201, [], ['groups' => 'entry:read']);
    }

    #[Route('/{id}', name: 'api_entries_update', methods: ['PUT'])]
    public function update(
        DailyEntry $entry,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        // Manual ownership check (no Voter needed)
        if ($entry->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['co2Value'])) {
            $entry->setCo2Value($data['co2Value']);
        }
        if (isset($data['details'])) {
            $entry->setDetails($data['details']);
        }
        if (isset($data['category'])) {
            $entry->setCategory($data['category']);
        }
        if (isset($data['date'])) {
            $entry->setEntryDate(new \DateTime($data['date']));
        }

        $em->flush();

        return $this->json($entry, 200, [], ['groups' => 'entry:read']);
    }

    #[Route('/{id}', name: 'api_entries_delete', methods: ['DELETE'])]
    public function delete(
        DailyEntry $entry,
        EntityManagerInterface $em
    ): JsonResponse {
        // Manual ownership check (no Voter needed)
        if ($entry->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        $em->remove($entry);
        $em->flush();

        return $this->json(['message' => 'Entry deleted'], 200);
    }

    #[Route('/today', name: 'api_entries_today', methods: ['GET'])]
    public function today(
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $today = new \DateTime('today');

        $entries = $em->getRepository(DailyEntry::class)->findBy([
            'user' => $user,
            'entryDate' => $today
        ]);

        $data = $serializer->serialize($entries, 'json', ['groups' => 'entry:read']);

        return new JsonResponse($data, 200, [], true);
    }
}
