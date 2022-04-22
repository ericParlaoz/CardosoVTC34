<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\Course;
use App\Form\CommandeType;
use App\Form\CourseType;
use App\Service\GetDistance;
use App\Service\UniqueIdService;
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
            //$form->get('adresseDepart')->getData();
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
            if ($kmTotal === 0 || $adress_1 === false || $adress_2 === false )  {
                // Message d'erreur si une des conditions est remplie
                $this->addFlash('error', "Erreur: Veillez renseigner par des adresses valides !");

            }
            else if ( $kmTotal > 800  ) {
                // Message d'erreur si une des conditions est remplie
                $this->addFlash('error', "Erreur: Grande distance sur devis");
            }
            else if ( $kmDepart > 400  ) {
                // Message d'erreur si une des conditions est remplie
                $this->addFlash('error', "Erreur: Ville de départ trop éloigné");
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
    public function newCourse(SessionInterface $session, Request $request, EntityManagerInterface $entityManager, GetDistance $distance, UniqueIdService $uniqueIdService): Response
    {
       if(empty($session->get('adresseDepart')) && empty($session->get('adresseArrivee'))) {
       return $this->redirectToRoute('accueil');
      }

        $adress_1 = $session->get('adresseDepart');
        $adress_2 = $session->get('adresseArrivee');
        $date = $session->get('date');


        $dateConvertion = date_format($date, "d/m/Y/H:i");

        // Calcul de la distance du départ à l'arrivée
        $distanceBrut = $distance->apiCalculDistance($adress_1, $adress_2);
        $duree = $distance->apiCalculDuree($adress_1,$adress_2);

        // J'additionne mes deux valeurs'
        $price = ($distanceBrut * 2);
        $priceArrondie = floor($price);
        //dd($price);


        // Je fait une condition qui affiche 20€ pour toute commande inferieur à 20€
        if($priceArrondie < 20){
            $priceArrondie = 20;
        }

        // Je stock le prix la distance brut de la course dans une variable
        $kmTotal = $distance->apiCalculDistance($adress_1, $adress_2);
        // j'arrondie la distance et la stock dans une autre variable
        $kmArrondie = floor($kmTotal);

        $client = new Clients();

        // Création de mon formulaire
        $form = $this->createForm(CommandeType::class, $client);
        // Hydratation de mon formulaire
        $form->handleRequest($request);

        // Si le formulaire et soumis et valide :
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            $id = $session->getId();
           // $uniqueID = $uniqueIdService->generateRandomID($id);

           // $session->set('id', $uniqueID);



            // Redirection sur la page de paiement et génération d'un ID
            return $this->redirectToRoute('checkout', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservations/course.html.twig', [
            'formulaireCommande' => $form->createView(),
            'date' => $dateConvertion,
            'adresseDepart' => $adress_1,
            'adresseArrivee' => $adress_2,
            'prix' => $priceArrondie,
            'distance' => $kmArrondie,
            'duree' => $duree
        ]);


    }

}

