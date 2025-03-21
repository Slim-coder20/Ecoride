<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\DriverInfoType;


final class UserController extends AbstractController
{
    #[Route('/mon-espace', name: 'app_user_dashboard')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        //Récupérer l'utilisateur connecté 
        /** @var User $user */
        $user= $this->getUser(); 

        // vérification pour s'assurer que l'utilisateur est bien connecté 

        if(!$user){
        
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        
        }
        
        
        // formulaire pour les informations du chauffeur 
        $form = $this->createForm(DriverInfoType::class, $user); 
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($user); 
            $em->flush(); 

            $this->addFlash('success', 'Vos informations ont été mis à jour avec succès.');

             // Redirection pour éviter une double soumission
             return $this->redirectToRoute('app_user_dashboard');
        }
        
            
            return $this->render('user/dashboard.html.twig', [
            'user' => $user, 
            'form' => $form->createView(), 
        ]);
    }
}
