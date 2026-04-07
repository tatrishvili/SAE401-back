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
        // Données de base pour tes défis
        $challengeTemplates = [
            ['Transport', 'Prendre le vélo aujourd\'hui', 'transport', 2.5],
            ['Alimentation', 'Faire un repas 100% végétarien', 'meal', 1.8],
            ['Énergie', 'Éteindre toutes les veilles ce soir', 'energy', 0.5],
            ['Déchets', 'Utiliser un sac réutilisable', 'waste', 0.3],
        ];

        for ($i = 1; $i <= 30; $i++) {
            $step = new Step();
            $step->setPosition($i);
            $step->setTitle("Jour " . $i);
            
            // Le jour 1 est débloqué par défaut, les autres sont verrouillés
            $step->setIsUnlocked($i === 1);
            
            // NOUVEAU : On définit que le jour n'est pas encore terminé
            $step->setIsCompleted(false); 

            $manager->persist($step);

            // On crée 2 défis pour CHAQUE jour en piochant dans nos templates
            for ($j = 0; $j < 2; $j++) {
                $template = $challengeTemplates[array_rand($challengeTemplates)];
                
                $challenge = new Challenge();
                $challenge->setTitle($template[0] . " - Jour " . $i);
                $challenge->setDescription($template[1]);
                $challenge->setCategory($template[2]);
                $challenge->setCo2Reward($template[3]);
                $challenge->setIsDaily(true);
                
                // On lie le défi à la Step (le jour) actuelle
                $challenge->setStep($step);
                
                $manager->persist($challenge);
            }
        }

        // Création d'un badge de test
        $badge = new Badge();
        $badge->setName('Eco-Débutant');
        $badge->setDescription('Premier pas !');
        $badge->setImageName('badge_starter.png');
        $manager->persist($badge);

        // On envoie tout en base de données
        $manager->flush();
    }
}