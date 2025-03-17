<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LoginController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupération des erreurs de connexion s'il y en a
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupération du dernier identifiant saisi (email)
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
    * cette route nous permet de déconnecter notre utilisateur 
    */
    #[Route('/deconnexion', name: 'app_logout')]
    public function logout()
    {
        // Cette méthode peut rester vide, elle sera interceptée par le logout automatiquement
    }
}
