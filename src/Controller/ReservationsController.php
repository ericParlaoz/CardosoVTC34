<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Form\CommandeType;
use App\Form\CourseType;
use App\Service\GetDistance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
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

            $session->get('adresseDepart',[]);
            $session->set('adresseDepart', $adress_1);
            $session->get('adresseArrivee',[]);
            $session->set('adresseArrivee', $adress_2);
            $session->get('passagers',[]);
            $session->set('passagers', $passagers);
            $session->get('date',[]);
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
            else if ( $kmDepart > 500  ) {
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
    public function newCourse(SessionInterface $session, FlashBagInterface $flashBag, Request $request, EntityManagerInterface $entityManager, GetDistance $distance): Response
    {

        $adress_1 = $session->get('adresseDepart',[]);
        $adress_2 = $session->get('adresseArrivee',[]);
        $date = $session->get('date',[]);

        $dateConvertion = date_format($date, "d/m/Y/H:i");

        // Calcul de la distance du départ à l'arrivée
        $distanceBrut = $distance->apiCalculDistance($adress_1, $adress_2);
        // Calcul de la distance de la ville du siège (Frontignan) à l'adresse de départ de la course
        $distanceDepart = $distance->depart($adress_1);


        // J'additionne mes deux valeurs'
        $price = ($distanceBrut + $distanceDepart) *1.2;
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

        $flashBag->add('success',"Date: $dateConvertion");
        $flashBag->add('success',"Adresse de départ: $adress_1");
        $flashBag->add('success',"Adresse d'arrivée: $adress_2");
        $flashBag->add('success',"Prix: $priceArrondie €");
        $flashBag->add('success',"Distance: $kmArrondie Km");


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
            return $this->redirectToRoute('checkout', ['id' => $client->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservations/course.html.twig', [
            'formulaireCommande' => $form->createView(),
        ]);
    }

}

