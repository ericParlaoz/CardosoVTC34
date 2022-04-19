<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ClientsRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(Request $request, SendMailService $mail, EntityManagerInterface $entityManager)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        // On vérifie ici que le formulaire est envoyé et qu'il est valide
        if($form->isSubmitted() && $form->isValid()){

            $contactFormData = $form->getData();

            $entityManager->persist($contact);
            $entityManager->flush();

            $mail->send(
                $contactFormData->getEmail(),
                'reservation@cardosovtc34.fr',
                'vous avez reçu un email',
                'Sender : '.$contactFormData->getEmail().\PHP_EOL.


                $contactFormData->getMessage(),
                'text/plain'
            );

            $this->addFlash('success', 'Votre message a été envoyé');
            return $this->redirect('#contact');

        }
        return $this->render('pages/index.html.twig', [
            'form' => $form->createView()
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
