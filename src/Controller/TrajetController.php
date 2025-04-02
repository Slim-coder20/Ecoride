<?php

namespace App\Controller;

use App\Entity\Trajet;
use App\Services\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



final class TrajetController extends AbstractController
{
    #[Route('/trajet/{id}/annuler', name: 'trajet_annuler', methods: ['POST'])]
    public function annulerTrajet(Trajet $trajet, EntityManagerInterface $em, EmailService $emailService): Response
    {
        
        // Vérifier que l'utilisateur connecté est le chauffeur du trajet // 
        $user = $this->getUser();
        if ($trajet->getChauffeur() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à annuler ce trajet.");
        }
        
        // Marquer le trajet comme annulé //
        $trajet->setIsCancelled(true);
        
        // Notifier et Remboursser le participant // 
        foreach ($trajet->getParticipations() as $participation) {
            $passager = $participation->getUser();

            /// Rembourser le passager /// 
            $passager->setCredits($passager->getCredits() + 1);
            $emailService->sendNotificationEmail(
                $passager->getEmail(),
                'Trajet annulé',
               "Bonjour {$passager->getPseudo()},\n\nLe trajet {$trajet->getDepart()} ➡️ {$trajet->getArrivee()} a été annulé par le chauffeur.\nVous avez été remboursé d’un crédit.\n\nL’équipe EcoRide 🌱"
            );

            $em->persist($passager);
        }
        $em->persist($trajet);
        $em->flush();

        $this->addFlash('success', 'Le trajet a été annulé avec succès et les passagers ont été remboursés.');

        return $this->redirectToRoute('app_user_dashboard');
        
    }

    // cette route va servire à demarrer le trajet si l'utilisateur est le chauffeur du trajet ddepuis le dashboard // 
    
    #[Route('/trajet/{id}/demarrer', name: 'trajet_demarrer', methods: ['POST'])]
    public function demarrerTrajet(Trajet $trajet, EntityManagerInterface $em, EmailService $emailService): Response
    {
        
        // Vérifier que l'utilisateur connecté est le chauffeur du trajet // 
        $user = $this->getUser();
        if ($trajet->getChauffeur() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à annuler ce trajet.");
        }
        
        
        
        // Notifier et Remboursser le participant // 
        foreach ($trajet->getParticipations() as $participation) {
            $passager = $participation->getUser();

            
        }
        ;

        $this->addFlash('success', 'Le trajet a été démarrer avec succès un mail a été envoiyé au passager.');

        return $this->redirectToRoute('app_user_dashboard');
        
    }





















































}
