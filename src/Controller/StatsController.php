<?php
namespace App\Controller;

use App\Entity\DailyEntry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    #[Route('/api/stats', name: 'api_stats', methods: ['GET'])]
    public function stats(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        $entries = $em->getRepository(DailyEntry::class)->findBy(
            ['user' => $user],
            ['entryDate' => 'DESC']
        );

        // Format all entries as flat list for the frontend
        $rawData = array_map(fn($e) => [
            'date'     => $e->getEntryDate()->format('Y-m-d'),
            'category' => $e->getCategory(),
            'co2'      => $e->getCo2Value(),
            'details'  => $e->getDetails(),
        ], $entries);

        // Daily (today)
        $today = (new \DateTime())->format('Y-m-d');
        $todayEntries = array_filter($rawData, fn($e) => $e['date'] === $today);
        $dailyCo2 = array_sum(array_column(iterator_to_array(
            new \ArrayIterator($todayEntries)), 'co2'));

        // Weekly (last 7 days)
        $weekAgo = (new \DateTime('-7 days'))->format('Y-m-d');
        $weekEntries = array_filter($rawData, fn($e) => $e['date'] >= $weekAgo);
        $weeklyCo2 = array_sum(array_column(iterator_to_array(
            new \ArrayIterator($weekEntries)), 'co2'));

        // Monthly (last 30 days)
        $monthAgo = (new \DateTime('-30 days'))->format('Y-m-d');
        $monthEntries = array_filter($rawData, fn($e) => $e['date'] >= $monthAgo);
        $monthlyCo2 = array_sum(array_column(iterator_to_array(
            new \ArrayIterator($monthEntries)), 'co2'));

        // Yearly (last 365 days)
        $yearAgo = (new \DateTime('-365 days'))->format('Y-m-d');
        $yearEntries = array_filter($rawData, fn($e) => $e['date'] >= $yearAgo);
        $yearlyCo2 = array_sum(array_column(iterator_to_array(
            new \ArrayIterator($yearEntries)), 'co2'));

        return $this->json([
            'entries'  => $rawData,   // full list for charts
            'summary'  => [
                'daily'   => round($dailyCo2, 2),
                'weekly'  => round($weeklyCo2, 2),
                'monthly' => round($monthlyCo2, 2),
                'yearly'  => round($yearlyCo2, 2),
            ]
        ]);
    }
}
