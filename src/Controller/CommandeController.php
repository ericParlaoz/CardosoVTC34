<?php

namespace App\Controller;

use App\Entity\Calendrier;
use App\Entity\Clients;
use App\Entity\Course;
use App\Form\CalendrierType;
use App\Form\CommandeType;
use App\Repository\CalendrierRepository;
use App\Repository\CourseRepository;
use App\Service\GetDistance;
use App\Service\Price;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\AST\Functions\DateDiffFunction;
use Doctrine\ORM\Query\Expr\OrderBy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'commande', methods: ['GET', 'POST'])]
    public function clientForm(Request $request, EntityManagerInterface $entityManager, CourseRepository $courseRepository, GetDistance $distance, Price $price): Response
    {
        // Je crée un client sur mon entité Client
        $client = new Clients();
        // Création de mon formulaire
        $form = $this->createForm(CommandeType::class, $client);
        // Hydratation de mon formulaire
        $form->handleRequest($request);

        // Si le formulaire et soumis et valide :
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('checkout', [], Response::HTTP_SEE_OTHER);
        }

        $result = $price->prixCourse($courseRepository,$distance);
        $distance = $price->distance($courseRepository,$distance);

        // j' affiche sur ma page les infos récoltées
        $this->addFlash('notice',"Distance total:"." ". $distance. " km" );
        $this->addFlash('notice',"Prix de la course:"." ". $result. " €" );

        // j'affiche mon rendu :
        // 1- Mon formulaire
        // 2 - Les infos de ma dernière course enregistrée (voir les détails dans le twig)
        return $this->render('commande/index.html.twig', [
            'formulaireCommande' => $form->createView(),
            'resultat' => $courseRepository->findBy([], ['id' => 'DESC'], 1),

        ]);
    }
}