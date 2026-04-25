<?php

namespace App\DataFixtures;

use App\Entity\Step;
use App\Entity\Challenge;
use App\Entity\Badge;
use App\Entity\Tip;
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

        // Badges de progression pour tester la gamification
        $badges = [
            ['Eco-Débutant', 'Premier pas !', '/images/badges/badge_starter.png', 0],
            ['Eco-Régulier', 'Tu commences à prendre le pli.', '/images/badges/badge_regular.png', 50],
            ['Eco-Expert', 'La transition écologique est bien lancée.', '/images/badges/badge_expert.png', 150],
        ];

        foreach ($badges as [$name, $description, $imageUrl, $xpThreshold]) {
            $badge = new Badge();
            $badge->setName($name);
            $badge->setDescription($description);
            $badge->setImageUrl($imageUrl);
            $badge->setXpThreshold($xpThreshold);

            $manager->persist($badge);
        }

        // Conseils (Tips) — chargés depuis un tableau pour rester en BDD
        $tips = [
            ['Transports', "Adopter des modes de transport plus écologiques est l'un des moyens les plus efficaces pour réduire ses émissions de CO₂. Pour les trajets courts, marcher ou faire du vélo permet d'éviter complètement les émissions. Pour les distances plus longues, privilégier les transports en commun ou le covoiturage permet de réduire le nombre de voitures en circulation."],
            ['Alimentation', "L'alimentation a un impact important sur l'empreinte carbone, notamment à cause de la production de viande et du transport des aliments. Réduire sa consommation de produits animaux, en particulier la viande rouge, permet de diminuer significativement ses émissions."],
            ['Energie', "La consommation d'énergie dans le logement représente une part importante des émissions de CO₂. Des gestes simples peuvent faire la différence : éteindre les appareils en veille, utiliser des ampoules LED, ajuster le chauffage. Une bonne isolation permet aussi de réduire durablement la consommation d'énergie."],
            ['Consommation', "Chaque produit acheté a un impact carbone lié à sa fabrication, son transport et sa fin de vie. Réduire sa consommation, privilégier des objets durables et éviter les produits jetables permet de limiter ces émissions."],
            ['Déchets', "La gestion des déchets influence directement les émissions de CO₂. Réduire ses déchets, trier correctement et recycler permet de limiter l'impact environnemental. Utiliser des objets réutilisables et éviter les emballages plastiques contribue également à diminuer les émissions."],
            ['Organisation', "Une meilleure organisation du quotidien permet de réduire facilement son empreinte carbone. Regrouper les courses, planifier ses déplacements ou anticiper ses besoins évite les actions inutiles et énergivores."],
            ['Suivi', "Comprendre et suivre son empreinte carbone est essentiel pour agir efficacement. En identifiant les activités les plus polluantes, il devient plus facile de changer ses habitudes."],
            ['Numérique', "Un usage plus responsable du numérique permet de réduire une part souvent sous-estimée des émissions de CO₂. Limiter le streaming en haute définition, supprimer les emails inutiles et conserver ses appareils plus longtemps sont des gestes simples mais efficaces."],
            ['Eau', "L'eau chaude sanitaire représente une part importante de la consommation énergétique d'un foyer. Réduire la durée de ses douches, installer des équipements économes et éviter les températures trop élevées permet de limiter à la fois la consommation d'eau et d'énergie."],
            ['Equipement', "Le choix des équipements électroménagers influence fortement la consommation d'énergie sur le long terme. Privilégier des appareils efficaces, bien les entretenir et utiliser les programmes éco contribue à limiter les émissions."],
            ['Voyage', "Le tourisme peut représenter une part importante de l'empreinte carbone, notamment à cause des transports. Privilégier des destinations proches, voyager moins souvent mais plus longtemps, et choisir des modes de transport moins polluants permet de réduire significativement son impact."],
            ['Alimentation', "La réduction du gaspillage alimentaire est un levier important pour limiter les émissions de CO₂. Planifier ses repas, conserver correctement les aliments et cuisiner les restes permet d'éviter de jeter de la nourriture."],
            ['Energie', "Le chauffage est l'un des principaux postes de consommation d'énergie dans un logement. Adapter la température selon les pièces, réduire légèrement le chauffage et améliorer l'isolation permet de diminuer fortement les émissions."],
            ['Consommation', "Les vêtements ont eux aussi une empreinte carbone importante liée à leur production. Acheter moins de vêtements, privilégier la qualité, réparer ou acheter d'occasion permet de limiter cet impact."],
            ['Transports', "L'utilisation de la voiture individuelle est une source majeure d'émissions. Réduire son usage en privilégiant les alternatives, adopter une conduite souple et entretenir son véhicule permet de limiter son impact."],
            ['Déchets', "La production de déchets électroniques est en forte augmentation. Réparer ses appareils, prolonger leur durée de vie et les recycler correctement permet de limiter leur impact environnemental."],
            ['Organisation', "Les gestes du quotidien, même les plus simples, ont un impact cumulé important sur l'empreinte carbone. Éteindre la lumière en quittant une pièce, débrancher les appareils inutilisés ou optimiser l'utilisation des ressources permet de réduire progressivement ses émissions."],
        ];

        foreach ($tips as [$title, $text]) {
            $tip = new Tip();
            $tip->setTitle($title);
            $tip->setText($text);
            $tip->setTag('general');
            $manager->persist($tip);
        }

        // On envoie tout en base de données
        $manager->flush();
    }
}