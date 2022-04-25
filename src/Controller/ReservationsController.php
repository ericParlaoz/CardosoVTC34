<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\Course;
use App\Form\CommandeType;
use App\Form\CourseType;
use App\Service\GetDistance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
class ReservationsController extends AbstractController
{
    #[Route('/', name: 'reservation')]
    public function newCourseBDD(Request $request, GetDistance $distance)
    {

        // Création de mon formulaire
        $form = $this->createForm(CourseType::class);
        // Hydratation de mon formulaire
        $form->handleRequest($request);


        // Si le formulaire et soumis et valide :
        if ($form->isSubmitted() && $form->isValid()) {


            // Je recupère ici les infos saisies dans mon formulaire
            $data = $form->getData();
            $adress_1  = $data->getAdresseDepart();
            $adress_2  = $data->getAdresseArrivee();
            $passagers = $data->getPassagers();
            $date = $data->getDate();

            // je crée ici la session
            $session = $request->getSession();

            $session->set('adresseDepart', $adress_1);
            $session->set('adresseArrivee', $adress_2);
            $session->set('passagers', $passagers);
            $session->set('date', $date);

            $kmTotal = $distance->apiCalculDistance($adress_1, $adress_2);
            $kmDepart = $distance->depart($adress_1);

            // Mes conditions
            if ($kmTotal === 0.0 || $adress_1 === false || $adress_2 === false )  {
                $this->addFlash('error', "Erreur: Veillez renseigner par des adresses valides !");

            }
            else if ( $kmTotal > 800  ) {
                $this->addFlash('error', "Erreur: Grande distance sur devis");
            }
            else if ( $kmDepart > 400  ) {
                $this->addFlash('error', "Erreur: Ville de départ non prise en charge !");
            }

            else if ( $kmDepart > 200 && $kmTotal < 150  ) {
                $this->addFlash('error', "Erreur: La distance de la course n'est pas suffisante !");
            }

            else {
                return $this->redirectToRoute('reservation2');
            }
        }

        return $this->render('reservations/index.html.twig', [
            'formulaireCourse' => $form->createView(),
        ]);
    }

    #[Route('/commande', name: 'reservation2')]
    public function newCourse(SessionInterface $session, Request $request, EntityManagerInterface $entityManager, GetDistance $distance): Response
    {
       if(empty($session->get('adresseDepart')) && empty($session->get('adresseArrivee'))) {
       return $this->redirectToRoute('accueil');
      }
        $id = $session->getId();
        $adress_1 = $session->get('adresseDepart');
        $adress_2 = $session->get('adresseArrivee');
        $date = $session->get('date');
        $dateConvertion = date_format($date, "d/m/Y/H:i");

        $km = $distance->apiCalculDistance($adress_1, $adress_2);
        $duree = $distance->apiCalculDuree($adress_1,$adress_2);
        $prix = $distance->apiCalculPrix($adress_1, $adress_2);

        $client = new Clients();

        // Création de mon formulaire
        $form = $this->createForm(CommandeType::class, $client);
        // Hydratation de mon formulaire
        $form->handleRequest($request);

        // Si le formulaire et soumis et valide :
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();


            // Redirection sur la page de paiement et génération d'un ID
            return $this->redirectToRoute('checkout', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservations/course.html.twig', [
            'formulaireCommande' => $form->createView(),
            'date' => $dateConvertion,
            'adresseDepart' => $adress_1,
            'adresseArrivee' => $adress_2,
            'prix' => $prix,
            'distance' => $km,
            'duree' => $duree
        ]);


    }

}

