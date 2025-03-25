<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

final class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_registration')]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user); 
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hashage du mot de passe
            $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            // Sauvegarde en base de données
            $em->persist($user);
            $em->flush();

            // Ajout d'un message flash
            $this->addFlash('success', 'Votre compte a été créé avec succès. Connectez-vous pour accéder à votre espace personnel.');

            // Redirection vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // Affichage du formulaire d'inscription
        return $this->render('registration/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}
