<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Repository\ClientsRepository;
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


    #[Route('/pdf/{unique_id}', name: 'pdf_id')]
    public function pdfId(Clients $client): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->setDefaultFont('Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($pdfOptions);

        $html = $this->renderView('pdf/id.html.twig', [
            'client' => $client
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream( "Facture");

        return new Response('', 200, [
            'Content-Type' => 'pdf/index.html.twig',
        ]);
     // return $this->render('pdf/index.html.twig',[
     //]);
    }
}
