<?php

namespace App\Controller;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/compta')]
class ComptaController extends AbstractController
{
    #[Route('/', name: 'compta_index')]
    public function index(FactureRepository $factureRepository): Response
    {

        return $this->render('administrator/compta/index.html.twig', [
            'factures' => $factureRepository->findBy([],['id' => 'DESC'])
        ]);
    }

    #[Route('/2022', name: 'compta_2022')]
    public function compta2022(FactureRepository $factureRepository): Response
    {
        $result = $factureRepository->findBy(['date_compta' => '2022'],['id' => 'DESC']);
        //dd($result);

        $totalRecettes = 0;
        foreach ($result as $value){
           $totalRecettes += $value->getPrix();
        }
        $totalCourses = $factureRepository->countByCourses();

        return $this->render('administrator/compta/2022.html.twig', [
            'factures' => $result,
            'recettes' => $totalRecettes,
            'courses' => $totalCourses
        ]);
    }


}
