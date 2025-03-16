<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_registration')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            // récupérer le mot de passe en claire depuis le formulaire 
            $plainPassword = $form->get('plainPassword')->getData();
           
            if ($plainPassword){
                 // hasher le mot de passe 
                $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $em->persist($user);
            $em->flush();

            //Ajouter un message flash
            $this->addFlash('success', 'Votre compte a été créé avec succès. Connectez-vous pour accéder à votre espace personnel.');

            return $this->redirectToRoute('app_login');
        }


        return $this->render('registration/index.html.twig', [
        'form' => $form->createView(),
        ]);
    }
}
