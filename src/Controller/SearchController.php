<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\RideRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/home/rechercher', name: 'app_search')]
    public function index(Request $request, RideRepository $rideRepository): Response
    {
        // Création du formulaire de recherche
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $rides = []; // Initialisation des résultats

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $data = $form->getData();

            // Recherche des trajets correspondant aux critères
            $rides = $rideRepository->findRidesByCriteria(
                $data['departure'],
                $data['destination'],
                $data['date']
            );

            // Si aucun trajet trouvé, proposer des alternatives
            if (empty($rides)) {
                $rides = $rideRepository->findClosestRides($data['date']);
                $this->addFlash('info', 'Aucun trajet trouvé. Voici les trajets les plus proches de votre recherche.');
            }
        }

        // Rendu de la vue avec le formulaire et les résultats
        return $this->render('search/search.html.twig', [
            'form' => $form->createView(),
            'rides' => $rides,
        ]);
    }
}
