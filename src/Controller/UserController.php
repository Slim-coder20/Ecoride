<?php

namespace App\Controller;

use App\Entity\Chauffeur;
use App\Form\DriverInfoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    #[Route('/mon-espace', name: 'app_user_dashboard')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Si l'utilisateur n'a pas encore de profil chauffeur, on le crée
        $chauffeur = $user->getChauffeur();
        if (!$chauffeur) {
            $chauffeur = new Chauffeur();
            $chauffeur->setUser($user);
            $user->setChauffeur($chauffeur);
            $em->persist($chauffeur);
        }

        // Création et traitement du formulaire
        $form = $this->createForm(DriverInfoType::class, $chauffeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Vos informations de chauffeur ont bien été mises à jour ✅');

            return $this->redirectToRoute('app_user_dashboard');
        }

        return $this->render('user/dashboard.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
