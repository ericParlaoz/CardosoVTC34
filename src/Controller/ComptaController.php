<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Form\CommandeType;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/compta')]
class ComptaController extends AbstractController
{
    #[Route('/', name: 'compta_index')]
    public function index(): Response
    {
        return $this->render('administrator/compta/index.html.twig');
    }

    #[Route('/2022', name: 'compta_2022')]
    public function compta2022(FactureRepository $factureRepository): Response
    {
        // Je stock dans la variable result toutes les courses de 2022 classé par ID récent au plus ancien
        // Je l'envoie dans ma vue
        $result = $factureRepository->findBy(['date_compta' => '2022'], ['id' => 'DESC']);
        // je stock dans la variable $date tous les résultats -1 (pour une condition)
        $date = $factureRepository->findBy(['date_compta' => '2022'], ['id' => 'DESC'],100,1 );

        // Je boucle tous les prix des courses présentes et je les additionne
        $totalRecettes = 0;
        foreach ($result as $value) {
            $totalRecettes += $value->getPrix();
        }

        $totalDates = "";
        foreach ($date as $value) {
                $totalDates .= $value->getDate();
        }
        // Je fait appel à une méthode crée dans factureRepository pour compter le nombre de lignes inscrites en bdd
        // afin d'afficher dans ma vue les nombres de courses
        $totalCourses = $factureRepository->countByCourses();

        return $this->render('administrator/compta/2022.html.twig', [
            'factures' => $result,
            'recettes' => $totalRecettes,
            'courses' => $totalCourses,
            'dates' => $totalDates
        ]);
    }

    #[Route('/{id}/avoir', name: 'facture_avoir', methods: ['GET', 'POST'])]
    public function edit(Facture $facture, EntityManagerInterface $entityManager): Response
    {

        $clone = clone $facture;
        $prix = $clone->getPrix();

        $prixNegatif = -$prix;
        $facture->setPrix($prixNegatif);

        $entityManager->persist($clone);
        $entityManager->flush();

        return $this->redirectToRoute('compta_index', [], Response::HTTP_SEE_OTHER);
    }
}
