<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/session')]
class SessionController extends AbstractController
{

    #[Route('/', name: 'session')]
    public function card(CourseRepository $courseRepository): Response
    {

        $course = $courseRepository->findBy([], ['id' => 'DESC'], 1);
        $result = $course[0]->getId();
       // dd($result);

        return $this->render('session/index.html.twig', [
            'course' => $result,
        ]);
    }

    #[Route('/add/{id}', name: 'add',requirements: ['id' => '\d+'])]
    public function add($id, CourseRepository $courseRepository, SessionInterface $session, FlashBagInterface $flashBag): Response
    {

        $course = $courseRepository->find($id);


        if(!$course) {
            throw $this->createNotFoundException("La course $id nexiste pas");
        }

        $courseADD = $session->get('course',[]);

        if(array_key_exists($id, $courseADD)) {
            $courseADD[$id]++;
        } else {
            $courseADD[$id]= 1;
            }

        $depart = $course->getAdresseDepart();
        $arrivee = $course->getAdresseArrivee();
        $duree = $course->getDuree();

        $flashBag->add('success',"Adresse de départ: $depart");
        $flashBag->add('success',"Adresse de d'arrivée: $arrivee");
        $flashBag->add('success',"Durée: $duree");
        $flashBag->add('success',"Prix: $duree");

        $flashBag->add('info', "test" );
        $flashBag->add('warning', "test2");
        //dd($flashBag);

        //dd($session->getBag('flashes'));

        $session->set('course', $courseADD);


        //$session->remove('course');

        //dd($session->get('course'));
        return $this->render('session/card.html.twig', [

        ]);
    }
}
