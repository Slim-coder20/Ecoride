<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class LoginController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupération des erreurs de connexion s'il y en a
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupération du dernier identifiant saisi (email)
        $lastUsername = $authenticationUtils->getLastUsername();

        // Vérifier si l'utilisateur est déjà connecté
        $user = $this->getUser();
        if ($user) {
            // Redirection en fonction des rôles
            if (in_array('ROLE_EMPLOYE', $user->getRoles())) {
                return $this->redirectToRoute('app_employe'); // Dashboard employé
            }

            return $this->redirectToRoute('app_user_dashboard'); // Dashboard utilisateur
        }

        // Afficher la page de connexion si l'utilisateur n'est pas connecté
        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Cette route nous permet de déconnecter notre utilisateur
     */
    #[Route('/deconnexion', name: 'app_logout')]
    public function logout()
    {
        // Cette méthode peut rester vide, elle sera interceptée par le système de sécurité
    }
}
