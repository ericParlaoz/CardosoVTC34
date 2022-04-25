<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Form\ClientsType;
use App\Form\CommandeType;
use App\Repository\ClientsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/clients')]
class ClientsController extends AbstractController
{
    #[Route('/', name: 'clients_index', methods: ['GET'])]
    public function index(ClientsRepository $clientsRepository): Response
    {


        return $this->render('administrator/clients/index.html.twig', [
            'clients' => $clientsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'clients_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Clients();
        $form = $this->createForm(CommandeType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('clients_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('administrator/clients/new.html.twig', [
            'client' => $client,
            'formulaireClient' => $form,
        ]);
    }

    #[Route('/{id}', name: 'clients_show', methods: ['GET'])]
    public function show(Clients $client): Response
    {

        return $this->render('administrator/clients/show.html.twig', [
            'client' => $client,

        ]);
    }

    #[Route('/{id}/edit', name: 'clients_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Clients $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('administrator/clients_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('administrator/clients/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'clients_delete', methods: ['POST'])]
    public function delete(Request $request, Clients $client, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('clients_index', [], Response::HTTP_SEE_OTHER);
    }
}
