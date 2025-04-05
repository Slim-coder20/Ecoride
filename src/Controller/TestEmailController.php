<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\EmailService;


final class TestEmailController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function index(EmailService $emailService): Response
    {
        $emailService->sendTemplatedEmail(

            'slimabida21@gmail.com', 
            'test email',
            'emails/test_email.html.twig', 
            []
            
        );
        
        return new Response('Email sent successfully!');
        
    }
}
