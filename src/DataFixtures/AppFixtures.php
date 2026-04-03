<?php

namespace App\DataFixtures;

use App\Entity\Step;
use App\Entity\Challenge;
use App\Entity\Badge;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 30; $i++) {
            $step = new Step();
            $step->setPosition($i);
            $step->setTitle("Jour " . $i);
            $step->setIsUnlocked($i === 1);
            $manager->persist($step);
        }

        $challenges = [
            ['Transport', 'Prendre le vélo', 'transport', 2.5],
            ['Alimentation', 'Repas végétarien', 'meal', 1.8],
            ['Énergie', 'Éteindre les veilles', 'energy', 0.5],
        ];

        foreach ($challenges as $c) {
            $challenge = new Challenge();
            $challenge->setTitle($c[0]);
            $challenge->setDescription($c[1]);
            $challenge->setCategory($c[2]);
            $challenge->setCo2Reward($c[3]);
            $challenge->setIsDaily(true);
            $manager->persist($challenge);
        }

        $badge = new Badge();
        $badge->setName('Eco-Débutant');
        $badge->setDescription('Premier pas !');
        $badge->setImageName('badge_starter.png');
        $manager->persist($badge);

        $manager->flush();
    }
}