<?php

namespace App\Controller;

use App\Entity\Trajet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CovoiturageType;
use Doctrine\ORM\Mapping as ORM;

final class CovoiturageController extends AbstractController
{   
    // c'est une route qui va nous permettre de faire une recherche de trajet à partir d'un formulaire //
    #[Route('/covoiturage', name: 'app_covoiturage')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(CovoiturageType::class); 
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

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

    // c'est une route qui va nous permettre de voir les résultats de la recherche //

    #[Route('/covoiturage/recherche', name: 'covoiturage_resultats')]
    public function resultats(Request $request, ManagerRegistry $doctrine): Response
    {
        $depart = $request->query->get('depart');
        $arrivee = $request->query->get('arrivee');
        $date = $request->query->get('date');
    
        $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
    
        if (!$dateObj) {
            $this->addFlash('error', 'La date saisie est invalide.');
            return $this->redirectToRoute('app_covoiturage');
        }
    
        $resultats = $doctrine->getRepository(Trajet::class)
            ->findByRecherche($depart, $arrivee, $dateObj); // ← ici on envoie bien $dateObj
    
        return $this->render('covoiturage/resultats.html.twig', [
            'resultats' => $resultats,
        ]);
    }
    

    // c'est une route qui va nous  rmettre de voir les détails d'un trajet // 
    
    #[Route('/covoiturage/{id}', name: 'covoiturage_details')]
    public function details(Trajet $trajet): Response
    {
        return $this->render('covoiturage/details.html.twig', [
            'trajet' => $trajet,
        ]);
    }
}
