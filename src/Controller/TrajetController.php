<?php

namespace App\Controller;

use App\Entity\Trajet;
use App\Services\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TrajetController extends AbstractController
{
    #[Route('/trajet/{id}/annuler', name: 'trajet_annuler', methods: ['POST'])]
    public function annulerTrajet(Trajet $trajet, EntityManagerInterface $em, EmailService $emailService): Response
    {
        // Vérifier que l'utilisateur connecté est le chauffeur du trajet
        $user = $this->getUser();
        if ($trajet->getChauffeur() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à annuler ce trajet.");
        }

        // Marquer le trajet comme annulé
        $trajet->setIsCancelled(true);

        // Notifier et rembourser le participant
        foreach ($trajet->getParticipations() as $participation) {
            $passager = $participation->getUser();

            // Rembourser le passager
            $passager->setCredits($passager->getCredits() + 1);
            $emailService->sendNotificationEmail(
                $passager->getEmail(),
                'Trajet annulé',
                "Bonjour {$passager->getPseudo()},\n\nLe trajet {$trajet->getDepart()}  {$trajet->getArrivee()} a été annulé par le chauffeur.\nVous avez été remboursé d’un crédit.\n\nL’équipe EcoRide 🌱"
            );

            $em->persist($passager);
        }
        $em->persist($trajet);
        $em->flush();

        $this->addFlash('success', 'Le trajet a été annulé avec succès et les passagers ont été remboursés.');

        return $this->redirectToRoute('app_user_dashboard');
    }

    // Cette route va servir à démarrer le trajet si l'utilisateur est le chauffeur du trajet depuis le dashboard
    #[Route('/trajet/{id}/demarrer', name: 'trajet_demarrer', methods: ['POST'])]
    public function demarrerTrajet(Trajet $trajet, EntityManagerInterface $em, EmailService $emailService, Request $request): Response
    {
        // Vérifier que l'utilisateur connecté est le chauffeur du trajet avec l'envoi de notification par mail au passager
        $user = $this->getUser();
        if ($trajet->getChauffeur() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à annuler ce trajet.");
        }

        // Mise de la sécurité contre les attaques CSRF
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('demarrer_' . $trajet->getId(), $submittedToken)) {
            throw $this->createAccessDeniedException("Token CSRF invalide.");
        }

        // Change le statut du trajet de prévu à en cours
        $trajet->setStatut('En cours');

        $em->flush();

        // Notifier les passagers
        foreach ($trajet->getParticipations() as $participation) {
            $passager = $participation->getUser();
            $emailService->sendNotificationEmail(
                $passager->getEmail(),
                'Trajet en cours',
                "Bonjour {$passager->getPseudo()},\n\nLe trajet {$trajet->getDepart()}  {$trajet->getArrivee()} a été démarré par le chauffeur.\n\nL’équipe EcoRide vous souhaite un bon voyage 🌱"
            );
        }
        $this->addFlash('success', 'Le trajet a bien été démarré.');
        return $this->redirectToRoute('app_user_dashboard');
    }

    // Cette route va servir à terminer le trajet si l'utilisateur est le chauffeur du trajet depuis le dashboard avec un envoi de notification au passager
    #[Route('/trajet/{id}/terminer', name: 'trajet_terminer', methods: ['POST'])]
    public function terminerTrajet(
        Request $request,
        Trajet $trajet,
        EntityManagerInterface $em,
        EmailService $emailService,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        $user = $this->getUser();

        // Vérifie si c’est bien le chauffeur et que le trajet est en cours
        if ($trajet->getChauffeur() !== $user || $trajet->getStatut() !== 'En cours') {
            throw $this->createAccessDeniedException("Action non autorisée.");
        }

        // Sécurité contre les attaques CSRF
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('terminer_' . $trajet->getId(), $submittedToken)) {
            throw $this->createAccessDeniedException("Token CSRF invalide.");
        }

        // Mise à jour du statut du trajet
        $trajet->setStatut('Terminé');

        // Envoie un mail à chaque passager
        foreach ($trajet->getParticipations() as $participation) {
            $passager = $participation->getUser();

            // Générer un lien vers le formulaire d'avis
            $lienAvis = $urlGenerator->generate('avis_soumettre', [
                'id' => $trajet->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            // Contenu HTML de l'email
            $htmlMessage = "
                <h1>Votre trajet est terminé</h1>
                <p>Bonjour {$passager->getPseudo()},</p>
                <p>Le trajet de {$trajet->getDepart()} à {$trajet->getArrivee()} s'est terminé. Nous vous invitons à donner votre avis sur ce trajet.</p>
                <p>
                    <a href='{$lienAvis}' style='display: inline-block; padding: 10px 15px; background-color: #77df0f; color: white; text-decoration: none; border-radius: 5px;'>
                        Donnez votre avis
                    </a>
                </p>
                <p>Merci pour votre participation avec EcoRide !</p>
            ";

            // Envoyer l'email
            $emailService->sendNotificationEmail(
                $passager->getEmail(),
                'Trajet terminé - Donnez votre avis',
                'emails/avis_notification.txt.twig', // chemin vers le template notification avis passager // 
                [
                    'passager' => $passager,
                    'trajet' => $trajet,
                    'lienAvis' => $lienAvis,
                ]
            );
        }

        // Sauvegarde
        $em->flush();

        // Redirection
        $this->addFlash('success', 'Trajet terminé. Les passagers ont été notifiés.');
        return $this->redirectToRoute('app_user_dashboard');
    }
}
