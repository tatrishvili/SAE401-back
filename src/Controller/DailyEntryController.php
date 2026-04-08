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
        $data = json_decode($request->getContent(), true);

        // Check if entry for this date + category already exists
        $existingEntry = $em->getRepository(DailyEntry::class)->findOneBy([
            'user' => $user,
            'entryDate' => new \DateTime($data['date'] ?? 'today'),
            'category' => $data['category']
        ]);

        if ($existingEntry) {
            return $this->json(['error' => 'Entry for this date and category already exists'], 409);
        }

        $entry = new DailyEntry();
        $entry->setUser($user);
        $entry->setEntryDate(new \DateTime($data['date'] ?? 'today'));
        $entry->setCategory($data['category']); // 'transport' or 'repas'
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
        $this->denyAccessUnlessGranted('edit', $entry);

        $data = json_decode($request->getContent(), true);

        if (isset($data['co2Value'])) {
            $entry->setCo2Value($data['co2Value']);
        }
        if (isset($data['details'])) {
            $entry->setDetails($data['details']);
        }

        $em->flush();

        return $this->json($entry, 200, [], ['groups' => 'entry:read']);
    }

    #[Route('/{id}', name: 'api_entries_delete', methods: ['DELETE'])]
    public function delete(
        DailyEntry $entry,
        EntityManagerInterface $em
    ): JsonResponse {
        $this->denyAccessUnlessGranted('delete', $entry);

        $em->remove($entry);
        $em->flush();

        return $this->json(['message' => 'Entry deleted'], 200);
    }
}
