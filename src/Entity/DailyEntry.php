<?php
// src/Entity/DailyEntry.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'daily_entries')]
#[ORM\Index(columns: ['user_id', 'entry_date'])]
class DailyEntry
{
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column(type: 'integer')]
#[Groups(['entry:read'])]
private ?int $id = null;

#[ORM\ManyToOne(targetEntity: User::class)]
#[ORM\JoinColumn(nullable: false)]
private ?User $user = null;

#[ORM\Column(type: 'date')]
#[Groups(['entry:read'])]
private ?\DateTimeInterface $entryDate = null;

#[ORM\Column(type: 'string', length: 20)]
#[Groups(['entry:read'])]
private ?string $category = null; // 'transport' or 'repas'

#[ORM\Column(type: 'float')]
#[Groups(['entry:read'])]
private ?float $co2Value = null; // Total CO2 in kg

#[ORM\Column(type: 'json')]
#[Groups(['entry:read'])]
private array $details = []; // Store transport ID + km OR selected foods

#[ORM\Column(type: 'datetime')]
#[Groups(['entry:read'])]
private ?\DateTimeInterface $createdAt = null;

public function __construct()
{
$this->createdAt = new \DateTime();
$this->entryDate = new \DateTime();
}

// Getters and setters...

public function getId(): ?int { return $this->id; }
public function getUser(): ?User { return $this->user; }
public function setUser(?User $user): self { $this->user = $user; return $this; }
public function getEntryDate(): ?\DateTimeInterface { return $this->entryDate; }
public function setEntryDate(\DateTimeInterface $entryDate): self { $this->entryDate = $entryDate; return $this; }
public function getCategory(): ?string { return $this->category; }
public function setCategory(string $category): self { $this->category = $category; return $this; }
public function getCo2Value(): ?float { return $this->co2Value; }
public function setCo2Value(float $co2Value): self { $this->co2Value = $co2Value; return $this; }
public function getDetails(): array { return $this->details; }
public function setDetails(array $details): self { $this->details = $details; return $this; }
public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
}
