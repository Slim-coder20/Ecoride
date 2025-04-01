<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Participation;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipationController extends AbstractController
{
    #[Route('/participation/{id}/valider', name: 'valider_participation', methods: ['POST'])]
    public function valider(Participation $participation, EntityManagerInterface $em): Response
    {
        // Vérification sécurité
        if ($participation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Accès refusé.");
        }

        $participation->setStatus('Validée');
        $participation->setConfirmation(true);

        $em->flush();

        $this->addFlash('success', 'Participation validée avec succès.');
        return $this->redirectToRoute('app_user_dashboard');
    }

    #[Route('/participation/{id}/annuler', name: 'annuler_participation', methods: ['POST'])]
    public function annuler(Participation $participation, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($participation->getUser() !== $user) {
            throw $this->createAccessDeniedException("Ce trajet ne vous appartient pas.");
        }

        // Mise à jour du statut et des données liées
        $participation->setStatus('Annulée');
        $participation->setConfirmation(false);

        // Crédit remboursé
        /** @var \App\Entity\User $user */
        $user->setCredits($user->getCredits() + 1);

        // Libérer la place
        $trajet = $participation->getTrajet();
        $trajet->setPlacesRestantes($trajet->getPlacesRestantes() + 1);

        $em->flush();

        $this->addFlash('info', 'Participation annulée.');
        return $this->redirectToRoute('app_user_dashboard');
    }

    





}
