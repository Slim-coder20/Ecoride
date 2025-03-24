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

    // c'est une route qui va nous permettre de voir les résultats de la recherche avec l'ajout de filtre pour peaufiner la selection du covoiturage //
    #[Route('/covoiturage/recherche', name: 'covoiturage_resultats')]
    public function resultats(Request $request, ManagerRegistry $doctrine): Response
{   
    $resultats = [];

    $depart = $request->query->get('depart');
    $arrivee = $request->query->get('arrivee');
    $date = $request->query->get('date');

    // Filtres supplémentaires
    $prixMax = $request->query->get('prix_max');
    $dureeMax = $request->query->get('duree_max'); // en heures
    $noteMin = $request->query->get('note_min');
    $ecologique = $request->query->get('ecologique');

    $dateObj = \DateTime::createFromFormat('Y-m-d', $date);

    if (!$dateObj) {
        $this->addFlash('error', 'La date saisie est invalide.');
        return $this->redirectToRoute('app_covoiturage');
    }

    $qb = $doctrine->getManager()->createQueryBuilder();
        $qb->select('t', 'c')
        ->from(Trajet::class, 't')
        ->join('t.chauffeur', 'c')
        ->where('t.depart = :depart')
        ->andWhere('t.arrivee = :arrivee')
        ->andWhere('t.dateDepart BETWEEN :startDate AND :endDate')
        ->setParameter('depart', $depart)
        ->setParameter('arrivee', $arrivee)
        ->setParameter('startDate', (clone $dateObj)->setTime(0, 0))
         ->setParameter('endDate', (clone $dateObj)->setTime(23, 59));

    // Filtres de base
    $qb->andWhere('t.depart = :depart')->setParameter('depart', $depart);
    $qb->andWhere('t.arrivee = :arrivee')->setParameter('arrivee', $arrivee);
    $qb->andWhere('t.dateDepart BETWEEN :startDate AND :endDate')
        ->setParameter('startDate', (clone $dateObj)->setTime(0, 0, 0))
        ->setParameter('endDate', (clone $dateObj)->setTime(23, 59, 59));

    // Filtres avancés
    if ($prixMax) {
        $qb->andWhere('t.prix <= :prixMax')->setParameter('prixMax', $prixMax);
    }

    if ($dureeMax) {
        $minutes = intval($dureeMax) * 60;
        $resultats = array_filter($resultats, function (Trajet $trajet) use ($dureeMax) {
            
            $depart = $trajet->getDateDepart();
            $arrivee = $trajet->getDateArrivee();

            $duree = ($arrivee->getTimestamp() - $depart->getTimestamp()) / 60;
            return $duree <= $dureeMax;
        });
    }

    if ($noteMin) {
        $qb->andWhere('c.note >= :noteMin')->setParameter('noteMin', $noteMin);
    }

    if ($ecologique) {
        $qb->andWhere('c.ecologique = true');
    }

    $resultats = $qb->getQuery()->getResult();

    return $this->render('covoiturage/resultats.html.twig', [
        'resultats' => $resultats,
    ]);
}

    
    // c'est une route qui va nous  permettre de voir les détails d'un trajet après sa séléction par l'utilisateur // 
    
    #[Route('/covoiturage/{id}', name: 'covoiturage_details')]
    public function details(Trajet $trajet): Response
    {
        return $this->render('covoiturage/details.html.twig', [
            'trajet' => $trajet,
        ]);
    }
}
