<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\Facture;
use App\Repository\ClientsRepository;
use App\Repository\FactureRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    #[Route('/pdf', name: 'pdf_index')]
    public function index(ClientsRepository $clientsRepository): Response
    {
        $clients = $clientsRepository->findAll();
       return $this->render('pdf/index.html.twig',[
           'clients' => $clients
       ]);
    }

    #[Route('/pdf-client/{unique_id}', name: 'pdf_id')]
    public function pdfClient(Clients $client): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->setDefaultFont('Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($pdfOptions);


        $html = $this->renderView('pdf/client.html.twig', [
            'client' => $client,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream( "Facture");

        return new Response('', 200, [
            'Content-Type' => 'pdf/client.html.twig',
        ]);
    }

    #[Route('/pdf-compta/{id}', name: 'pdf_compta_id')]
    public function pdfCompta(Facture $facture, ClientsRepository $clientsRepository): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->setDefaultFont('Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($pdfOptions);


        $html = $this->renderView('pdf/compta.html.twig', [
            'facture' => $facture,

        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream( "Facture");

        return new Response('', 200, [
            'Content-Type' => 'pdf/compta.html.twig',
        ]);
    }
}
