<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        // Création du formulaire de contact à partir de la classe ContactType
        $form = $this->createForm(ContactType::class);
        // Traitement des données POST
        $form->handleRequest($request);
        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $data = $form->getData();
            // Envoi du mail
            $email = (new Email())
                ->from($data['email'])
                ->to('contact.ecoride9@gmail.com')
                ->subject($data['subject'])
                ->text($data['message']);
            $mailer->send($email);

            // Ajout d'un message flash
            $this->addFlash('success', 'Votre message a bien été envoyé.');
            // Redirection vers la page de contact
            return $this->redirectToRoute('app_contact');
        }
        // Affichage du formulaire de contact   
        return $this->render('contact/contact.html.twig', [
            'formContact' => $form->createView(),
        ]);
    }
}
