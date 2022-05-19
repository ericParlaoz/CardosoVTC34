<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\Facture;
use App\Form\CommandeType;
use App\Form\CourseType;
use App\Service\CalculCourse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/course')]
class ReservationsController extends AbstractController
{
    #[Route('/', name: 'course')]
    public function newCourse(Request $request, CalculCourse $calcul)
    {
        // Création de mon formulaire de calcul de course
        $form = $this->createForm(CourseType::class);
        // Hydratation de mon formulaire
        $form->handleRequest($request);

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
            $kmTotal = $calcul->apiCalculDistance($adress_1, $adress_2);
            // Je fait ici le calcul de la ditance de départ et de la ville de départ du chauffeur privé pour mettre en place des conditions
            $kmDepart = $calcul->apiCaclulDepart($adress_1);

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
    public function newCommande(SessionInterface $session, Request $request, CalculCourse $calcul): Response
    {
        // Je corrige l'erreur d'accès à cette route (session obligatoire)
       if(empty($session->get('adresseDepart')) && empty($session->get('adresseArrivee'))) {
       return $this->redirectToRoute('accueil');
      }

        // Je récupère ici les infos enregistrées précedemment dans la session pour les afficher dans ma vue
        $id = $session->getId();
        $adress_1 = $session->get('adresseDepart');
        $adress_2 = $session->get('adresseArrivee');
        $date = $session->get('date');
        $dateConvertion = date_format($date, "d/m/Y/H:i");

        // Je convertis les dates pour les champs
        date_default_timezone_set('Europe/Paris');
        $dateReservation = date('Y/m/d');
        $dateCompta= date( "Y");

        // Je refais appel à mes méthodes de calcul dans mon service afin d'avoir : ma distance, ma durée, mon prix
        // Et les afficher dans ma vue
        $km = $calcul->apiCalculDistance($adress_1, $adress_2);
        $duree = $calcul->apiCalculDuree($adress_1,$adress_2);
        $prix = $calcul->apiCalculPrix($adress_1, $adress_2);

        // Je modifie le mot "hours" par "heures"
       $dureeVue = str_replace("hours", "heures", $duree);

        // Création de mon formulaire de renseignements clients
        $form = $this->createForm(CommandeType::class);
        $form->handleRequest($request);

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
            $facture->setDateReservation($dateReservation);
            $facture->setPrix($prix);
            $facture->setAdresseDepart($adress_1);
            $facture->setAdresseArrivee($adress_2);
            $facture->setDate($dateConvertion);
            $facture->setDateCompta($dateCompta);

            // création de mon objet (client) pour stockage en bdd si le paiement est validé
            $client = new Clients();
            $client->setNom($data['nom']);
            $client->setPrenom($data['prenom']);
            $client->setrue($data['rue']);
            $client->setCodepostal($data['codepostal']);
            $client->setVille($data['ville']);
            $client->setTelephone($data['telephone']);
            $client->setEmail($data['email']);
            $client->setDateReservation($dateReservation);
            $client->setPrix($prix);
            $client->setAdresseDepart($adress_1);
            $client->setAdresseArrivee($adress_2);
            $client->setDate($dateConvertion);
            $client->setConfidentialite($data['confidentialite']);
            $client->setInfos($data['infos']);
            $client->setDuree($duree);
            $client->setUniqueId($id);
            $client->setDateCompta($dateCompta);

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
            'duree' => $dureeVue,
        ]);
    }
}

