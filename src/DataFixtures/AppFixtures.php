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
        // création d'un utilisateur chauffeur pour les tests // 

        $chauffeur = new User();
        $chauffeur->setPseudo('chauffeur');
        $chauffeur->setEmail('JeanChauffeur'); 
        $chauffeur->setPassword('password');
        $chauffeur->setIsChauffeur(true);
        $chauffeur->setBrand('Peugeot');
        $chauffeur->setModel('208');
        $chauffeur->setLicensePlate('AB-123-CD');
        $chauffeur->setSeat(4);
        $chauffeur->setEnergie('électrique');
        $chauffeur->setEcologique(true);

        
        $manager->persist($chauffeur);

        // Création de plusieurs trajets pour les tests // 

        for($i = 0; $i < 20; $i++){
            $trajet = new Trajet();
            $trajet->setDepart('Paris');
            $trajet->setArrivee('Lyon');
            $trajet->setDateDepart(new \DateTime("+{$i} days 08:00"));
            $trajet->setDateArrivee(new \DateTime("+{$i} days 12:00"));
            $trajet->setPrix(20 + $i * 5);
            $trajet->setPlacesRestantes(3 - ($i % 3));
            $trajet->setChauffeur($chauffeur);

            $manager->persist($trajet);
        }

        


        $manager->flush();
    }
}
