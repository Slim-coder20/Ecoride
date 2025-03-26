<?php

namespace App\Controller;

use App\Entity\Chauffeur;
use App\Form\DriverInfoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
    // c'est une route qui nous sert à redireger l'utilisateur vers la page de détails du trajet sélectionné //

    #[Route('/mon-esapce/passager', name: 'espace_passager')]
    public function passager(Request $request): Response
    {
        $session = $request->getSession();
        $trajetId = $session->get('trajet_selected');  

        if($trajetId){
            return $this->redirectToRoute('covoiturage_details', ['id' => $trajetId]);
        
        }

        // si pas de trajet sélectionné, on redirige l'utilisateur vers la page de recherche de covoiturage  //
        $this->addFlash('warning', 'Aucun trajet sélectionné.');

        return $this->redirectToRoute('app_covoiturage');
        
    }

    // cette route permet de rediriger l'utilisateur vers la page de devenir chauffeur //

    #[Route('/espace/passager-chauffeur', name: 'espace_passager_chauffeur')]
    public function passagerChauffeur(): Response
    {
        $this->addFlash('info', '🚀 Vous avez choisi d’être à la fois passager et chauffeur.');
        
        return $this->redirectToRoute('app_become_driver');
    }
    
    // cette route nous sert a afficher les trajets sélectionnés par l'utilisateur avant qu'il ne se connecte à travers la session qu'on a créé pour stocker sa recherche de trajet //
    
    #[Route('/mon-espace/clear-trajet', name: 'clear_selected_trajet', methods: ['POST'])]
    public function clearSelectedTrajet(SessionInterface $session): Response
{
    $session->remove('trajet_selectionne');
    $this->addFlash('info', 'Trajet sélectionné annulé.');
    return $this->redirectToRoute('app_user_dashboard');
}
}