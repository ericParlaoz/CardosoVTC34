<?php

namespace App\Service;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendMailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer =$mailer;
    }

    public function send(string $from, string $to, string $subject, string $texte) : void
    {
        $message = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->text($texte);
        $this->mailer->send($message);
    }
}