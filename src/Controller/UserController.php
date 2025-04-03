<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Vehicule;
use App\Entity\Chauffeur;
use App\Entity\Trajet;

use App\Form\VehiculeType;
use App\Form\TrajetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class UserController extends AbstractController

{   
    // la route de la page d'accueil de l'utilisateur //
    #[Route('/mon-espace', name: 'app_user_dashboard')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Récupérer les trajets créé en tant que chauffeur //

        $trajetsChauffeur = []; 
        if ($user->getChauffeur()) {
            $trajetsChauffeur = $em->getRepository(Trajet::class)->findBy(['chauffeur' => $user->getChauffeur()]);
        }

        // Participations en tant que passager //
        $participations = $user->getParticipations();

        return $this->render('user/dashboard.html.twig', [
            'user' => $user,
            'trajetsChauffeur' => $trajetsChauffeur,
            'participations' => $participations,
        ]);
    }

    // la route de la page devenir chauffeur //

    #[Route('/chauffeur/devenir', name: 'app_become_driver')]
    public function becomeDriver(Request $request, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        if (!$user->getChauffeur()) {
            $chauffeur = new Chauffeur();
            $chauffeur->setUser($user);
            $user->setChauffeur($chauffeur);
            $em->persist($chauffeur);
        } else {
            $chauffeur = $user->getChauffeur();
        }

        $vehicule = new Vehicule();
        $vehicule->setChauffeur($chauffeur);

        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($vehicule);
            $em->flush();
            $this->addFlash('success', 'Véhicule enregistré avec succès.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        return $this->render('user/become_driver.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mon-esapce/passager', name: 'espace_passager')]
    public function passager(Request $request): Response
    {
        $session = $request->getSession();
        $trajetId = $session->get('trajet_selected');  

        if ($trajetId) {
            return $this->redirectToRoute('covoiturage_details', ['id' => $trajetId]);
        }

        $this->addFlash('warning', 'Aucun trajet sélectionné.');
        return $this->redirectToRoute('app_covoiturage');
    }

    // c'est une route qui nous permettre d'afficher le formulaire de selection passager et chaffeur au même temps // 

    #[Route('/espace/passager-chauffeur', name: 'espace_passager_chauffeur')]
    public function passagerChauffeur(): Response
    {
        $this->addFlash('info', '🚀 Vous avez choisi d’être à la fois passager et chauffeur.');
        return $this->redirectToRoute('app_become_driver');
    }

    
    
    // c'est une route qui nous permet de selectionner le trajet selectionner par l'utilisateur avant de se connecter //

    #[Route('/mon-espace/creer-trajet', name: 'app_user_create_trajet')]
    public function createTrajet(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
    
        //  Vérifie que l’utilisateur est bien un chauffeur
        $chauffeur = $user->getChauffeur();
        if (!$chauffeur) {
            $this->addFlash('warning', 'Vous devez d’abord devenir chauffeur avant de créer un trajet.');
            return $this->redirectToRoute('app_become_driver');
        }
    
        //  Création du trajet
        $trajet = new Trajet();
        $trajet->setChauffeur($user); // Associer le trajet au chauffeur
    
        $form = $this->createForm(TrajetType::class, $trajet);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($trajet);
            $em->flush();
    
            $this->addFlash('success', '🚗 Trajet enregistré avec succès.');
            return $this->redirectToRoute('app_user_dashboard');
        }
    
        return $this->render('user/create_trajet.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    
   
}
