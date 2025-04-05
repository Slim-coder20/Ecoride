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
    #[Route('/participation/{id}/valider', name: 'valider_participation', methods: ['POST'])]
    public function valider(Participation $participation, EntityManagerInterface $em, EmailService $emailService): Response
    {
        if ($participation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Accès refusé.");
        }

        $participation->setStatus('Validée');
        $participation->setConfirmation(true);

        $em->flush();

        // Test des données avant l'envoi de l'email
   

        try {
            $emailService->sendTemplatedEmail(
                $participation->getUser()->getEmail(),
                'Participation validée',
                'emails/participation_validee.html.twig',
                [
                    'participation' => $participation,
                ]
            );
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email.');
        }

        $this->addFlash('success', 'Participation validée avec succès.');
        return $this->redirectToRoute('app_user_dashboard');
    }

    #[Route('/participation/{id}/annuler', name: 'annuler_participation', methods: ['POST'])]
    public function annuler(Participation $participation, EntityManagerInterface $em, EmailService $emailService): Response
    {
        $user = $this->getUser();

        if ($participation->getUser() !== $user) {
            throw $this->createAccessDeniedException("Ce trajet ne vous appartient pas.");
        }

        $participation->setStatus('Annulée');
        $participation->setConfirmation(false);
 
        $user->setCredits($user->getCredits() + 1);

        $trajet = $participation->getTrajet();
        $trajet->setPlacesRestantes($trajet->getPlacesRestantes() + 1);

        $em->flush();

        try {
            $emailService->sendTemplatedEmail(
                $user->getEmail(),
                'Participation annulée',
                'emails/participation_annulee.html.twig',
                [
                    'participation' => $participation,
                ]
            );
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email.');
        }

        $this->addFlash('info', 'Participation annulée.');
        return $this->redirectToRoute('app_user_dashboard');
    }

    #[Route('/participer/{id}', name: 'app_participer', methods: ['POST'])]
    public function participer(Trajet $trajet, EntityManagerInterface $em, EmailService $emailService): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour participer à un trajet.');
            return $this->redirectToRoute('app_login');
        }

        foreach ($user->getParticipations() as $participation) {
            if ($participation->getTrajet() === $trajet) {
                $this->addFlash('warning', 'Vous participez déjà à ce trajet.');
                return $this->redirectToRoute('app_user_dashboard');
            }
        }

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

        try {
            $emailService->sendTemplatedEmail(
                $user->getEmail(),
                'Participation au trajet',
                'emails/participation_confirmer.html.twig',
                [
                    'participation' => $participation,
                ]
            );
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email.');
        }

        $this->addFlash('success', 'Vous avez rejoint le trajet.');
        return $this->redirectToRoute('app_user_dashboard');
    }
}
