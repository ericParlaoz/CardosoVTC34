<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\Course;
use App\Entity\Facture;
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

        // Création de mon formulaire de calcul de course
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

            // J'enregistre ces infos dans la session
            $session->set('adresseDepart', $adress_1);
            $session->set('adresseArrivee', $adress_2);
            $session->set('passagers', $passagers);
            $session->set('date', $date);

            // Je fait appel à mon service de calcul de distance pour avoir la ditance totale
            // Grâce à ce resultat, je peut vérifier mes conditions
            $kmTotal = $distance->apiCalculDistance($adress_1, $adress_2);
            // Je fait ici le calcul de la ditance de départ et de la ville de départ du chauffeur privé pour mettre en place des conditions
            $kmDepart = $distance->depart($adress_1);

            // Mes différebtes conditions
            if ($kmTotal === 0.0 || $adress_1 === false || $adress_2 === false )  {
                $this->addFlash('error', "Erreur: Veillez renseigner par des adresses valides !");

            }
            else if ( $kmTotal > 800  ) {
                $this->addFlash('error', "Grande distance sur devis");
            }
            else if ( $kmDepart > 400  ) {
                $this->addFlash('error', "Erreur: Ville de départ non prise en charge !");
            }

            else if ( $kmDepart > 200 && $kmTotal < 150  ) {
                $this->addFlash('error', "La distance de la course n'est pas suffisante !");
            }

            // Redirection si on ne rentre pas dans les conditions précédentes
            else {
                return $this->redirectToRoute('commande');
            }
        }

        return $this->render('reservations/index.html.twig', [
            'formulaireCourse' => $form->createView(),
        ]);
    }

    #[Route('/commande', name: 'commande')]
    public function newCommande(SessionInterface $session, Request $request, GetDistance $distance): Response
    {
        // Je corrige l'erreur d'accès à cette route si on passe pas par la page précédente
       if(empty($session->get('adresseDepart')) && empty($session->get('adresseArrivee'))) {
       return $this->redirectToRoute('accueil');
      }

        // Je récupère ici les infos enregistrés précedemment dans la session pour les afficher dans ma vue
        $id = $session->getId();
        $adress_1 = $session->get('adresseDepart');
        $adress_2 = $session->get('adresseArrivee');
        $date = $session->get('date');
        $dateConvertion = date_format($date, "d/m/Y/H:i");

        // Je refait appel à mes méthodes de calcul dans mon service afin d'avoir :
        // 1- La ditance
        // 2- La durée
        // 3- Le prix
        // Et les afficher dans ma vue
        $km = $distance->apiCalculDistance($adress_1, $adress_2);
        $duree = $distance->apiCalculDuree($adress_1,$adress_2);
        $prix = $distance->apiCalculPrix($adress_1, $adress_2);

        // Création de mon formulaire de renseignements clients
        $form = $this->createForm(CommandeType::class);
        // Hydratation de mon formulaire
        $form->handleRequest($request);

        // Si le formulaire et soumis et valide :
        if ($form->isSubmitted() && $form->isValid()) {

            // Je crée une variable afin d'accéder aux infos des champs du formulaire
            $data = $form->getData();

            // création de mon objet (facture) pour stockage en bdd si le paiement est validé
            $facture = new Facture();
            $facture->setNom($data['nom']);
            $facture->setPrenom($data['prenom']);
            $facture->setrue($data['rue']);
            $facture->setCodepostal($data['codepostal']);
            $facture->setVille($data['ville']);
            $facture->setTelephone($data['telephone']);
            $facture->setEmail($data['email']);
            $facture->setDateReservation($data['date_reservation']);
            $facture->setPrix($data['prix']);
            $facture->setAdresseDepart($data['adresse_depart']);
            $facture->setAdresseArrivee($data['adresse_arrivee']);
            $facture->setDate($data['date']);
            $facture->setDateCompta($data['date_compta']);

            // création de mon objet (client) pour stockage en bdd si le paiement est validé
            $client = new Clients();
            $client->setNom($data['nom']);
            $client->setPrenom($data['prenom']);
            $client->setrue($data['rue']);
            $client->setCodepostal($data['codepostal']);
            $client->setVille($data['ville']);
            $client->setTelephone($data['telephone']);
            $client->setEmail($data['email']);
            $client->setDateReservation($data['date_reservation']);
            $client->setPrix($data['prix']);
            $client->setAdresseDepart($data['adresse_depart']);
            $client->setAdresseArrivee($data['adresse_arrivee']);
            $client->setDate($data['date']);
            $client->setConfidentialite($data['confidentialite']);
            $client->setInfos($data['infos']);
            $client->setDuree($data['duree']);
            $client->setUniqueId($data['unique_id']);
            $client->setDateCompta($data['date_compta']);

            // j'enregistre dans la session mes deux objets afin de pouvoir les enregistrer plus tard (si le paiement est accepté)
            $session->set('facture', $facture);
            $session->set('client', $client);

            // Redirection sur la page de paiement et génération d'un ID (ID complexe= Id de la session)
            return $this->redirectToRoute('checkout', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservations/commande.html.twig', [
            'formulaireCommande' => $form->createView(),
            'date' => $dateConvertion,
            'adresseDepart' => $adress_1,
            'adresseArrivee' => $adress_2,
            'prix' => $prix,
            'distance' => $km,
            'duree' => $duree,

        ]);
    }

}

