<?php

namespace App\Controller;

use App\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/rechercher', name: 'app_search')]
    public function index(Request $request, ): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // traitement des données ici 
            $data = $form->getData();
            // effectuer une recherche dans la base de données et passez les résultats à la vue
        
        }
        
            return $this->render('search/search.html.twig', [
            'controller_name' => 'SearchController',
            'form' => $form->createView(),
        ]);
    }
}
