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

        // Cette condition permet de rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté // 
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        return $this->render('user/dashboard.html.twig', [
            'user' => $user,
        ]);

        
    }

    // création de la route pour devenir chauffeur //
    
    #[Route('/chauffeur/devenir', name: 'app_become_driver')]
    public function becomeDriver(Request $request, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Cette condition permet de rediriger l'utilisateur vers la page de connexion s'il n'est pas connecté // 
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // créations de l'objet chauffeur si inexistant //

        if (!$user->getChauffeur()) {
            $chauffeur = new Chauffeur();
            $chauffeur->setUser($user);
            $user->setChauffeur($chauffeur);
            $em->persist($chauffeur);
        } else {
            $chauffeur = $user->getChauffeur();
        }

        $form = $this->createForm(DriverInfoType::class, $chauffeur);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Vos infos chauffeur ont bien été enregitrées.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        return $this->render('user/become_driver.html.twig',[
            'form' => $form->createView(),
        ]);

        
        
    }
}