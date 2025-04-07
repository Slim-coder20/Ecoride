<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Participation;
use App\Entity\Trajet;
use App\Services\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class ParticipationController extends AbstractController
{
    #[Route('/participation/{id}/valider', name: 'valider_participation', methods: ['POST'])]
    public function valider(Participation $participation, EntityManagerInterface $em, EmailService $emailService, LoggerInterface $logger): Response
    
    {   

        // ON verifie d'abord si l'utilisateur est connecté //
        if ($participation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Accès refusé.");
        }

        $participation->setStatus('Validée');
        $participation->setConfirmation(true);

        $em->flush();
        
        // on met le try catch pour vérifier si le mail est bien envoyé au passager  // 
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
            $logger->error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email.');
        }
         
        // On envoie un message de succés ç l'utilisateur //
        
        $this->addFlash('success', 'Participation validée avec succès.');
        return $this->redirectToRoute('app_user_dashboard');
    }

    // C'est une route qui permet d'annuler une participation depuis le dashboard du user // 

    #[Route('/participation/{id}/annuler', name: 'annuler_participation', methods: ['POST'])]
    public function annuler(Participation $participation, EntityManagerInterface $em, EmailService $emailService, LoggerInterface $logger): Response
    {
        $user = $this->getUser();

        // Vérification si l'utilisateur est connecté // 

        if ($participation->getUser() !== $user) {
            throw $this->createAccessDeniedException("Ce trajet ne vous appartient pas.");
        }

        

        $participation->setStatus('Annulée');
        $participation->setConfirmation(false);
        
        /** @var \App\Entity\User $user */
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
            $logger->error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email.');
        }

        $this->addFlash('info', 'Participation annulée.');
        return $this->redirectToRoute('app_user_dashboard');
    }

    // C'est une route qui permet de participer à un trajet depuis le dashboard du user //

    #[Route('/participer/{id}', name: 'app_participer', methods: ['POST'])]
    public function participer(Trajet $trajet, EntityManagerInterface $em, EmailService $emailService, LoggerInterface $logger): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour participer à un trajet.');
            return $this->redirectToRoute('app_login');
        }
   
         
        /** @var \App\Entity\User $user */
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
        
        /** @var \App\Entity\User $user */
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
            $logger->error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email.');
        }

        $this->addFlash('success', 'Vous avez rejoint le trajet.');
        return $this->redirectToRoute('app_user_dashboard');
    }
}
