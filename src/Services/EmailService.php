<?php 

namespace App\Services;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EmailService
{
    private $mailer; 
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        $transport = Transport::fromDsn($this->params->get('MAILER_DSN'));
        $this->mailer = new Mailer($transport);
    }

    public function sendContactEmail(string $from, string $subject, string $message): void
    {
        $email = (new TemplatedEmail())
            ->from($this->params->get('CONTACT_EMAIL'))
            ->replyTo($from)
            ->to($this->params->get('CONTACT_EMAIL'))
            ->subject($subject)
            ->text($message);

        $this->mailer->send($email);
    }

    public function sendNotificationEmail(string $to, string $subject, string $message): void
    {
        $email = (new TemplatedEmail())
            ->from($this->params->get('CONTACT_EMAIL'))
            ->to($to)
            ->subject($subject)
            ->text($message);

        // Envoi de l'email

        $this->mailer->send($email);
    }

    public function sendTemplatedEmail(string $to, string $subject, string $template, array $context): void
    {
        $email = (new TemplatedEmail())
            ->from($this->params->get('CONTACT_EMAIL'))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        $this->mailer->send($email);
    }
}