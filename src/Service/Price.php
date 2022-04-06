<?php

namespace App\Service;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class Price
{

    function prixCourse(SessionInterface $session, GetDistance $distance)
    {
        //$result = $courseRepository->findBy([], ['id' => 'DESC'], 1);

        $adresse1 = $session->get('adresseDepart',[]);
        $adresse2 = $session->get('adresseArrivee',[]);

        //$adresse1 = $result[0]->getAdresseDepart() ;
        //$adresse2= $result[0]->getAdresseArrivee() ;


        $kmTotal = $distance->apiCalcul($adresse1, $adresse2);

        //j'arrondis mon résultat distance
      $kmArrondie = floor($kmTotal);
      $price = $kmArrondie *2;

      if($price < 20){
          $price = 20;
      }

        return  $price;
    }

    function distance(SessionInterface $session, CourseRepository $courseRepository, GetDistance $distance)
    {
        //$result = $courseRepository->findBy([], ['id' => 'DESC'], 1);

        //$adresse1 = $result[0]->getAdresseDepart() ;
        //$adresse2= $result[0]->getAdresseArrivee() ;

        $adresse1 = $session->get('adresseDepart',[]);
        $adresse2 = $session->get('adresseArrivee',[]);

        $kmTotal = $distance->apiCalcul($adresse1, $adresse2);

        //j'arrondis mon résultat distance
        $kmArrondie = floor($kmTotal);

        return  $kmArrondie;
    }
}