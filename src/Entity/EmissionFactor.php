<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EmissionFactor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $category = null; // 'transport', 'food'

    #[ORM\Column(length: 100)]
    private ?string $slug = null; // 'voiturethermique', 'meat'

    #[ORM\Column(length: 100)]
    private ?string $labelFr = null;

    #[ORM\Column(length: 100)]
    private ?string $labelEn = null;

    #[ORM\Column(length: 10)]
    private ?string $icon = null; // '🚗', '🥩'

    #[ORM\Column]
    private ?float $factorPerUnit = null; // kg CO2 par km ou par repas

    #[ORM\Column(length: 20)]
    private ?string $unit = null; // 'km', 'meal'

    // Getters et setters...
}
