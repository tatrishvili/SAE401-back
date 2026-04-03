<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ActivityRepository;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $category = null; // 'transport' ou 'repas'

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $type = null; // 'voiturethermique', 'meat', etc.

    #[ORM\Column(nullable: true)]
    private ?float $distance = null; // pour transport (km)

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null; // pour repas (nombre de repas)

    #[ORM\Column]
    private ?float $co2Emitted = null; // kg CO2

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 5)]
    private ?string $locale = null; // 'fr' ou 'en'

    // Getters et setters...
}
