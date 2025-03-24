<?php

namespace App\Controller;
use \DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trajet;
use Symfony\Component\Security\Core\Security;
use App\Entity\Participation;



    class ParticipationController extends AbstractController
{
    #[Route('/trajet/{id}/participer', name: 'participer_trajet',  methods: ['POST'])]
    public function participer(Trajet $trajet, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();
        // 1-  On vérifie si l'utilisateur est connecté // 
        if (!$security->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour participer à ce trajet.'); 
            // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
            
            return $this->redirectToRoute('app_login');
        }
        
        // 2- On vérfie les conditions pour participer au trajet// 

        if ($trajet->getPlacesRestantes() < 1 )
        {
            $this->addFlash('error', 'Conditions non remplies pour participer à ce trajet.');
            return $this->redirectToRoute('covoiturage_details', ['id' => $trajet->getId()]);
        }

        if($user->getCredits() < 1)
        {
            $this->addFlash('error', "Vous n'avez pas assez de crédits pour participer à ce trajet. Veuilez rechergare votre crédits."); 
            return $this->redirectToRoute('TODO', ['id' => $trajet->getId()]);
        
        }

        // 3- création de la paticipation // 

        $participation = new Participation(); 
        $participation->setUser($user);
        $participation->setTrajet($trajet);
        $participation->setDateParticipation(new DateTime());
        $participation->setConfirmation(false); // Pour la double confirmation 

        // 4- Mise à jour des places restantes et des crédits //

        $user->setCredits($user->getCredits() - 1);
        $trajet->setPlacesRestantes($trajet->getPlacesRestantes() - 1);

        // 5- Enregistrement en base de données //

        $em->persist($participation);
        $em->flush();
        
        
        
        
        
        $this->addFlash('success', 'Votre participation a bien été enregistrée.');
        return $this->redirectToRoute('covoiturage_details', ['id' => $trajet->getId()]);
    }
}
