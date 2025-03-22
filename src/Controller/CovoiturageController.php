<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CovoiturageType;

final class CovoiturageController extends AbstractController
{
    #[Route('/covoiturage', name: 'app_covoiturage')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(CovoiturageType::class); 
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        
    {
        $data = $form->getData();
        
      // Redirection vers la page de résultats, avec les données en GET
      return $this->redirectToRoute('covoiturage_resultats', [
        'depart' => $data['depart'],
        'arrivee' => $data['arrivee'],
        'date' => $data['date']->format('Y-m-d'),
    ]);
   
    }
        
        return $this->render('covoiturage/covoiturage.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
    // c'est une route qui va nous servire a afficher le reusltat de la recherche du trajet par l'utilisateur après avoir cliqué sur le bouton "Rechercher"
    
    
    #[Route('/covoiturage/recherche', name: 'covoiturage_resultats')]
    public function resultats(Request $request): Response
{
    $depart = $request->query->get('depart');
    $arrivee = $request->query->get('arrivee');
    $date = $request->query->get('date');

    // Appel au repository pour filtrer les trajets
    $resultats = $this->getDoctrine()->getRepository(Covoiturage::class)
                    ->findByRecherche($depart, $arrivee, new \DateTime($date));

    return $this->render('covoiturage/resultats.html.twig', [
        'resultats' => $resultats,
    ]);
}
    
    // création de la route du détails de la recherche du trajet par l'utilisateur après avoir cliqué sur le bouton "Voir les détails" 

    #[Route('/covoiturage/{id}', name: 'covoiturage_details')]
    public function details(Covoiturage  $trajet): Response
    {
        $trajet = $this->getDoctrine()->getRepository(Covoiturage::class)->find($id);
    
    }














}
