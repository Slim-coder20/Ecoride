<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Entity\Trajet;
use App\Services\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipationController extends AbstractController
{   
    // cette route permet de valiser une participation depuis l'espace utilisateur //
    
    #[Route('/participation/{id}/valider', name: 'valider_participation', methods: ['POST'])]
    public function valider(Participation $participation, EntityManagerInterface $em, EmailService $emailService): Response
    {   
         // Vérifie si l'utilisateur est connecté pour sécurisé la participation
        // et éviter les participations anonymes
        
        if ($participation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Accès refusé.");
        }

        $participation->setStatus('Validée');
        $participation->setConfirmation(true);

        $em->flush();

        // Envoi d'un email de notification à l'utilisateur//
        $emailService->sendNotificationEmail(
            $participation->getUser()->getEmail(),
            'Participation validée',
            ' Bonjour votre participation au trajet a été validée.'
        );


        $this->addFlash('success', 'Participation validée avec succès.');
        return $this->redirectToRoute('app_user_dashboard');
    }

    // cette route nous pemet d'annuler une participation après l'avoir selectionné lors de la recherche de trajet depuis l'espace utilisateur // 

    #[Route('/participation/{id}/annuler', name: 'annuler_participation', methods: ['POST'])]
    public function annuler(Participation $participation, EntityManagerInterface $em, EmailService $emailService): Response
    {
        $user = $this->getUser();
        
        // Vérifie si l'utilisateur est connecté pour sécurisé la participation
        // et éviter les participations anonymes
        
        if ($participation->getUser() !== $user) {
            throw $this->createAccessDeniedException("Ce trajet ne vous appartient pas.");
        }

        $participation->setStatus('Annulée');
        $participation->setConfirmation(false);

        $user->setCredits($user->getCredits() + 1);

        $trajet = $participation->getTrajet();
        $trajet->setPlacesRestantes($trajet->getPlacesRestantes() + 1);

        $em->flush();

        // Envoi d'un email de notification à l'utilisateur//
        
        $emailService->sendNotificationEmail(
            $user->getEmail(),
            'Participation annulée',
            'Bonjour, votre participation au trajet du ' . $trajet->getDateDepart()->format('d/m/Y') . ' a été annulée Vos crédits ont été remboursés.'
        );

        $this->addFlash('info', 'Participation annulée.');
        return $this->redirectToRoute('app_user_dashboard');
    }

    //cette route 

    #[Route('/participer/{id}', name: 'app_participer', methods: ['POST'])]
    public function participer(Trajet $trajet, EntityManagerInterface $em, EmailService $emailService): Response
    {
        $user = $this->getUser();
        // Vérifie si l'utilisateur est connecté pour sécurisé la participation
        // et éviter les participations anonymes
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour participer à un trajet.');
            return $this->redirectToRoute('app_login');
        }

        // Empêche les participations en double
        foreach ($user->getParticipations() as $participation) {
            if ($participation->getTrajet() === $trajet) {
                $this->addFlash('warning', 'Vous participez déjà à ce trajet.');
                return $this->redirectToRoute('app_user_dashboard');
            }
        }

        // Vérifie qu'il reste des places
        if ($trajet->getPlacesRestantes() <= 0) {
            $this->addFlash('danger', 'Aucune place restante pour ce trajet.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        $participation = new Participation();
        $participation->setUser($user);
        $participation->setTrajet($trajet);
        $participation->setStatus('En attente');
        $participation->setConfirmation(false);
        $participation->setdateParticipation(new \DateTime());

        $trajet->setPlacesRestantes($trajet->getPlacesRestantes() - 1);
        $user->setCredits($user->getCredits() - 1);

        $em->persist($participation);
        $em->flush();

        // envoi d'un email de notification à l'utilisateur// 
        $emailService->sendNotificationEmail(
            $user->getEmail(),
            'Participation au trajet',
            'Bonjour, vous avez rejoint le trajet du ' . $trajet->getDateDepart()->format('d/m/Y') . '.'
        );

        $this->addFlash('success', 'Vous avez rejoint le trajet.');
        return $this->redirectToRoute('app_user_dashboard');
    }
}
