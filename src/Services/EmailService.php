<?php 

namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer; 

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendContactEmail(string $from, string $subject, string $message): void
    {
        $email = (new Email())
            ->from($from)
            ->to($_ENV['CONTACT_EMAIL'])
            ->subject($subject)
            ->text($message);
        $this->mailer->send($email);
    }
    public function sendNotificationEmail(string $to, string $subject, string $message): void
    {
        $email = (new Email())
            ->from($_ENV['CONTACT_EMAIL'])
            ->to($to)
            ->subject($subject)
            ->text($message);
        $this->mailer->send($email);
    }







}