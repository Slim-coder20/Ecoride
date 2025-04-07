<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AvisRepository;
use App\Repository\TrajetRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EMPLOYE')]
final class EmployeController extends AbstractController
{
    #[Route('/employe', name: 'app_employe')]
    public function index(AvisRepository $avisRepository, TrajetRepository $trajetRepository): Response
    {
        $pendingReviews = $avisRepository->findBy(['status' => 'en attente']);
        $problematicRides = $trajetRepository->findProblematicRides();

        return $this->render('employe/dashboard.html.twig', [
            'pendingReviews' => $pendingReviews,
            'problematicRides' => $problematicRides,
        ]);
    }

    #[Route('/employe/avis', name: 'employe_reviews')]
    public function listPendingReviews(AvisRepository $avisRepository): Response
    {
        // Récupérer les avis en attente de validation
        $pendingReviews = $avisRepository->findBy(['status' => 'pending']);

        return $this->render('employe/reviews.html.twig', [
            'pendingReviews' => $pendingReviews,
        ]);
    }

    #[Route('/employe/avis/{id}/valider', name: 'employe_validate_review')]
    public function validateReview(int $id, AvisRepository $avisRepository): Response
    {
        $review = $avisRepository->find($id);
        if ($review) {
            $review->setStatus('validated');
            $avisRepository->save($review, true);
        }

        return $this->redirectToRoute('employe_reviews');
    }

    #[Route('/employe/avis/{id}/refuser', name: 'employe_reject_review')]
    public function rejectReview(int $id, AvisRepository $avisRepository): Response
    {
        $review = $avisRepository->find($id);
        if ($review) {
            $review->setStatus('rejected');
            $avisRepository->save($review, true);
        }

        return $this->redirectToRoute('employe_reviews');
    }

    #[Route('/employe/trajets-problematiques', name: 'employe_problematic_rides')]
    public function listProblematicRides(TrajetRepository $trajetRepository): Response
    {
        // Récupérer les trajets problématiques
        $problematicRides = $trajetRepository->findProblematicRides();

        return $this->render('employe/problematic_rides.html.twig', [
            'problematicRides' => $problematicRides,
        ]);
    }
}
