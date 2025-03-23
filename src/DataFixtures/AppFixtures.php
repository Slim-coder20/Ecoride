<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trajet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $photos = [
            'images/chauffeur_1.jpg',
            'images/chauffeur_2.jpg',
            'images/chauffeur_3.jpg'
        ];

        for ($i = 0; $i < 3; $i++) {
            $chauffeur = new User();
            $chauffeur->setPseudo('chauffeur' . $i);
            $chauffeur->setPhoto($photos[$i]);
            $chauffeur->setEmail('chauffeur' . $i . '@ecoride.com');
            $chauffeur->setPassword('password');
            $chauffeur->setIsChauffeur(true);
            $chauffeur->setBrand('Peugeot');
            $chauffeur->setModel('208');
            $chauffeur->setLicensePlate('AB-12' . $i . '-CD');
            $chauffeur->setSeat(4);
            $chauffeur->setEnergie('électrique');
            $chauffeur->setEcologique(true);
            $chauffeur->setNote(4.5 - $i * 0.3);

            $manager->persist($chauffeur);

            // 💡 Crée 5 trajets par chauffeur
            for ($j = 0; $j < 5; $j++) {
                $trajet = new Trajet();
                $trajet->setDepart('Paris');
                $trajet->setArrivee('Lyon');
                $trajet->setDateDepart(new \DateTime("+$j days 08:00"));
                $trajet->setDateArrivee(new \DateTime("+$j days 12:00"));
                $trajet->setPrix(20 + $j * 5);
                $trajet->setPlacesRestantes(3 - ($j % 3));
                $trajet->setChauffeur($chauffeur);

                $manager->persist($trajet);
            }
        }

        $manager->flush();
    }
}