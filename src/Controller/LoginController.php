<?php

namespace App\Controller;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;

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

    
    /**
     * Cette route nous permet de rénitialiser le mot de passe de l'utilisateur 
     * en cas d'oubli
    */
    #[Route('/motdepasseoublie', name: 'app_forgotten_password')]
    public function forgottenPassword(Request $request, UserRepository $userRepository): Response
    
    {
        // Initialiser le formulaire de réinitialisation du mot de passe
        $form = $this->createform(ResetPasswordRequestFormType::class);
        
        $form ->handleRequest($request);
        // Vérifier si le formulaire est soumis et valide 
        if($form->isSubmitted() && $form->isValid())
        {
        // le fomrulaire est envoyé et valide // 
        // On va chercher l''utilisateur en base de données // 
        $user = $userRepository->findOneByEmail($form->get('email')->getData());
           // On vérifie si on a bien un utilisateur // 
           if($user)
           {
            // On a trouvé un utiisateur // 
            // ON génère un token de rénitialisation de mot de passe // 
            

                
           }
           //$user = Null; 
           $this->addFlash('error', 'Un problème est survenu lors de la réinitialisation du mot de passe. Veuillez réessayer.');
           return $this->redirectToRoute('app_login');

         

        
        }

        
        
        
        // Afficher la page de réinitialisation du mot de passe
        return $this->render('login/forgotten_password.html.twig',[
            'form' => $form->createView(),
        ]);
    }






}
