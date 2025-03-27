<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class VehiculeController extends AbstractController
{
    #[Route('/vehicule/ajouter', name: 'vehicule_ajouter')]
    public function index(Request $request, EntityManagerInterface $em ): Response
    {
        $user = $this->getUser();

        // l'utilisateur doit être connecté pour accéder à cette page // 

        if(!$user || !$user->getChauffeur()){
            $this->addFlash('danger', 'Vous devez être chauffeur pour accéder à cette page');
            return $this->redirectToRoute('app_user_dashboard');
        }

        $vehicule = new Vehicule();
        $vehicule->setChauffeur($user->getChauffeur());


        // création du formulaire de véhicule à partir de la classe VehiculeType // 
        $fom = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($vehicule);
            $em->flush();

            $this->addFlash('success', 'Votre véhicule a bien été ajouté');
            return $this->redirectToRoute('app_user_dashboard');
        }

        
        return $this->render('vehicule/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
