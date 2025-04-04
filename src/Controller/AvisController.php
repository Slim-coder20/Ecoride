<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AvisType;
use App\Entity\Avis;

use App\Entity\Trajet;

final class AvisController extends AbstractController
{
    #[Route('/avis/trajet/{id}', name: 'avis_soumettre')]
    public function soumettreAvis(Request $request, Trajet $trajet, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // sécurité : seul un passager ayant participer au trajet peut soumettre un avis //  

        $participation = $trajet->getParticipations()->filter(fn ($p) => $p->getUser() === $user)->first();
        if(!$participation || $trajet->getStatus() !== 'Terminé') {
            throw $this->createAccessDeniedException('Vous ne pouvez pas soumettre un avis pour ce trajet.');
        }

        // Créer un nouvel avis // 
        $avis = new Avis();
        $avis->setdateCreation(new \DateTime());
        $avis->setStatus('en attente');
           
        // on créé notre formulaire AvisForm// 

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // On lie l'avis à l'utilisateur et au trajet
            $avis->setUtilisateur($user);
            $avis->setTrajet($trajet);

            
            // Enregistrer l'avis dans la base de données // 
            $em->persist($avis);
            $em->flush();
            
            $this->addFlash('success', 'Votre avis a été soumis avec succès ! Il sera examiné par notre équipe.');
            return $this->redirectToRoute('app_user_dashboard');
            
            
}
        return $this->render('avis/submit.html.twig', [
            'form' => $form->createView(),
            'trajet' => $trajet,
            
        ]);
    }
}
