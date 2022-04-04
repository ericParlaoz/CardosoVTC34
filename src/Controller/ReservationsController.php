<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\Course;
use App\Form\CommandeType;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Service\GetDistance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
class ReservationsController extends AbstractController
{
    #[Route('/', name: 'reservation')]
    public function newCourseBDD(Request $request, EntityManagerInterface $entityManager, GetDistance $distance)
    {
        // Je crée une course sur mon entité Course
        $course = new Course();

        // Création de mon formulaire
        $form = $this->createForm(CourseType::class, $course);
        // Hydratation de mon formulaire
        $form->handleRequest($request);


        // Si le formulaire et soumis et valide :
        if ($form->isSubmitted() && $form->isValid()) {

            // Je recupère ici les adresses saisies
            $adress_1 = $course->getAdresseDepart();
            $adress_2 = $course->getAdresseArrivee();

            // j'inscris en base de donnée la durée de la course
           $dureeBDD = $distance->apiCalculDuree($adress_1, $adress_2);
           $course->setDuree($dureeBDD);

            // Je stock le prix du calcul de la course dans une variable
            $price = ((int)$distance->apiCalcul($adress_1, $adress_2) * 2);
            // Je stock le prix la distance brut de la course dans une variable
            $kmTotal = $distance->apiCalcul($adress_1, $adress_2);
            // j'arondie la distance et la stock dans une autre variable
            $kmArrondie = floor($kmTotal);

            // Je fait une condition qui affiche 20€ pour toute commande inferieur à 20€
            if($price < 20){
                $price = 20;
            }

            $course->setPrix($price);


            if ($kmTotal === 0 || $adress_1 === false || $adress_2 === false || $dureeBDD === false )  {
                // Message d'erreur si une des conditions est remplie
                $this->addFlash('error', "Erreur: Veillez renseigner par des adresses valides !");

            }
            else if ( $kmTotal > 800 ) {
                // Message d'erreur si une des conditions est remplie
                $this->addFlash('error', "Erreur: Grande distance sur devis");
            }
            else {
                $entityManager->persist($course);
                $entityManager->flush();

                return $this->redirectToRoute('reservation_id', ['id' => $course->getId()], Response::HTTP_SEE_OTHER);
            }

        }

        return $this->render('reservations/index.html.twig', [
            'formulaireCourse' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'reservation_id', requirements: ['id' => '\d+'])]
    public function newCourse($id, CourseRepository $courseRepository, SessionInterface $session, FlashBagInterface $flashBag, Request $request, EntityManagerInterface $entityManager, Course $course): Response
    {
        $client = new Clients();
        // Création de mon formulaire
        $form = $this->createForm(CommandeType::class, $client);
        // Hydratation de mon formulaire
        $form->handleRequest($request);

        // Si le formulaire et soumis et valide :
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();


            // Redirection sur la page de paiement
            return $this->redirectToRoute('checkout', ['id' => $course->getId()], Response::HTTP_SEE_OTHER);
        }

        $course = $courseRepository->find($id);

        if(!$course) {
            throw $this->createNotFoundException("La course $id nexiste pas");
        }

        $courseADD = $session->get('course',['id']);

        if(array_key_exists($id, $courseADD)) {
            $courseADD[$id]++;
        } else {
            $courseADD[$id]= 1;
        }

        $depart = $course->getAdresseDepart();
        $arrivee = $course->getAdresseArrivee();
        $prix = $course->getPrix();
        $date = $course->getDate();
        $dateConvertion = date_format($date, "d/m/Y/H:i");

        $flashBag->add('success',"Date: $dateConvertion");
        $flashBag->add('success',"Adresse de départ: $depart");
        $flashBag->add('success',"Adresse d'arrivée: $arrivee");
        $flashBag->add('success',"Prix: $prix €");

        //$flashBag->add('info', "test" );
        //$flashBag->add('warning', "test2");
        //dd($flashBag);

        //dd($session->getBag('flashes'));

        $session->set('course', $courseADD);


        //dd($session->get('course'));
        return $this->render('reservations/course.html.twig', [
            'formulaireCommande' => $form->createView(),
        ]);
    }


}

