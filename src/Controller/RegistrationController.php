<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Récupérer les rôles sélectionnés dans le formulaire
            $roles = $form->get('roles')->getData();
            $validRoles = ['ROLE_USER', 'ROLE_EMPLOYE'];

            // Filtrer les rôles pour ne garder que ceux qui sont valides
            $roles = array_filter($roles, fn($role) => in_array($role, $validRoles));

            // Si aucun rôle n'est sélectionné, attribuer ROLE_USER par défaut
            if (empty($roles)) {
                $roles[] = 'ROLE_USER';
            }

            $user->setRoles($roles);

            // Sauvegarder l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger après l'inscription
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/registeration.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
