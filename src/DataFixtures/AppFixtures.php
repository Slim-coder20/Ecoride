<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Chauffeur;
use App\Entity\Trajet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $photosChauffeurs = [
            'images/chauffeur_1.jpg',
            'images/chauffeur_2.jpg',
            'images/chauffeur_3.jpg'
        ];

        $photosVoiture = [
            'images/voiture_1.jpg',
            'images/voiture_2.jpg',
            'images/voiture_3.jpg'
        ];

        for ($i = 0; $i < 3; $i++) {
            // Création de l'utilisateur
            $user = new User();
            $user->setPseudo('JeanYves32' . $i);
            $user->setEmail('chauffeur' . $i . '@ecoride.com');
            $user->setRoles(['ROLE_USER']);
            $hashedPassword = $this->hasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);

            $manager->persist($user);

            // Création du chauffeur lié à l'utilisateur
            $chauffeur = new Chauffeur();
            $chauffeur->setUser($user);
            $chauffeur->setPhoto($photosChauffeurs[$i]);
            $chauffeur->setLicensePlate('AB-12' . $i . '-CD');
            $chauffeur->setRegistrationDate(new \DateTime());
            $chauffeur->setModel('208');
            $chauffeur->setBrand('Peugeot');
            $chauffeur->setColor('Rouge');
            $chauffeur->setSeat(4);
            $chauffeur->setEnergie('électrique');
            $chauffeur->setEcologique(true);
            $chauffeur->setPreferences(['non-fumeur', 'pas d’animal']);

            $manager->persist($chauffeur);

            // Création des trajets associés au chauffeur
            for ($j = 0; $j < 5; $j++) {
                $trajet = new Trajet();
                $trajet->setDepart('Paris');
                $trajet->setArrivee('Lyon');
                $trajet->setPhotoVoiture($photosVoiture[$i]);
                $trajet->setDateDepart(new \DateTime("+$j days 08:00"));
                $trajet->setDateArrivee(new \DateTime("+$j days 12:00"));
                $trajet->setPrix(20 + $j * 5);
                $trajet->setPlacesRestantes(3 - ($j % 3));
                $trajet->setChauffeur($user); // On N'oublie pas que dans Trajet, le chauffeur est toujours un User

                $manager->persist($trajet);
            }
        }

        $manager->flush();
    }
}
