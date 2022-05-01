<?php

namespace App\Controller;

use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/compta')]
class ComptaController extends AbstractController
{
    #[Route('/', name: 'compta_index')]
    public function index(): Response
    {
        // Page d'index de la comptbilité
        return $this->render('administrator/compta/index.html.twig');
    }

    #[Route('/2022', name: 'compta_2022')]
    public function compta2022(FactureRepository $factureRepository): Response
    {
        // Je stock dans la variable result toutes les courses de 2022 classé par ID récent au plus ancien
        // Je l'envoie dans ma vue
        $result = $factureRepository->findBy(['date_compta' => '2022'],['id' => 'DESC']);

        // Je boucle tous les prix des courses présentes et je les additionne
        $totalRecettes = 0;
        foreach ($result as $value){
           $totalRecettes += $value->getPrix();
        }
        // Je fait appel à une méthode crée dans factureRepository pour compter le nombre de lignes inscrites en bdd
        // afin d' afficher dans ma vue les nombres de courses
        $totalCourses = $factureRepository->countByCourses();

        return $this->render('administrator/compta/2022.html.twig', [
            'factures' => $result,
            'recettes' => $totalRecettes,
            'courses' => $totalCourses
        ]);
    }


}
