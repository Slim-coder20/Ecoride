<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Vehicule;
use App\Form\VehiculeType;


final class VehiculeController extends AbstractController
{
    #[Route('/vehicule/ajouter', name: 'vehicule_ajouter')]
    public function index(Request $request, EntityManagerInterface $em ): Response
    {
        $user = $this->getUser();

        // l'utilisateur doit être connecté pour accéder à cette page // 

        if(!$user || !$user->getChauffeur()){
            $this->addFlash('danger', 'Vous devez être chauffeur pour accéder à cette page');
            return $this->redirectToRoute('app_user_dashboard');
        }
        
        // création de l'enité véhicule // 
        $vehicule = new Vehicule();
        $vehicule->setChauffeur($user->getChauffeur());


        // création du formulaire de véhicule à partir de la classe VehiculeType // 
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            /** @var UploadedFile $photoFile */
            
            $photoFile = $form->get('photoFile')->getData();

            if($photoFile){
            
                    $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();


                    //deplacer le fichier vers le répertoire où les photos sont stockées //

                    $photoFile->move(
                        $this->getParameter('photo_directory'),
                        $newFilename
                    );

                    // Enregistrer le nom de l'entité // 

                    $vehicule->setPhoto($newFilename);
                }
            }
            $em->persist($vehicule);
            $em->flush();

         
            return $this->render('vehicule/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
