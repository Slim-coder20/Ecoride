<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailService
{
    private MailerInterface $mailer;
    private string $from;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->from = 'contact.ecoride9@gmail.com'; // fixé ici pour simplifier
    }

    public function sendTemplatedEmail(string $to, string $subject, string $template, array $context = []): void
    {
        $email = (new TemplatedEmail())
            ->from($this->from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        $this->mailer->send($email);
    }

    public function sendTextEmail(string $to, string $subject, string $message): void
    {
        $email = (new TemplatedEmail())
            ->from($this->from)
            ->to($to)
            ->subject($subject)
            ->text($message);

        $this->mailer->send($email);
    }
}
