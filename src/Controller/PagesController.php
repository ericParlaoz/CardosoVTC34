<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(Request $request, MailerInterface $mailer)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        // On vérifie ici que le formulaire est envoyé et qu'il est valide
        if($form->isSubmitted() && $form->isValid()){

            $emailContact =$contact->getEmail();
            $emailTel = $contact->getTelephone();
            $emailMessage = $contact->getMessage();
            $emailNom = $contact->getNom();

            $email = (new TemplatedEmail())
                ->from('contact@cardosovtc34.fr')
                ->to(new Address('contact@cardosovtc34.fr'))
                ->subject('Nouveau message')
                ->htmlTemplate('email/index.html.twig')
                ->context([
                    'emailrender' => $emailContact,
                    'telrender' => $emailTel,
                    'messagerender' => $emailMessage,
                    'nomrender' => $emailNom
                ]);


            $mailer->send($email);

            $this->addFlash('success', 'Votre message a été envoyé');
            return $this->redirect('#contact');

        }
        return $this->render('pages/index.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    #[Route('/mentions', name: 'mentions')]
    public function mentions(): Response
    {
        return $this->render('pages/mentions.html.twig');
    }

    #[Route('confidentialite', name: 'confidentialite')]
    public function confidentialite(): Response
    {
        return $this->render('pages/confidentialite.html.twig');
    }

}
