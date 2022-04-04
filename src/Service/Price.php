<?php

namespace App\Service;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Price
{

    function prixCourse(CourseRepository $courseRepository, GetDistance $distance)
    {
        $result = $courseRepository->findBy([], ['id' => 'DESC'], 1);

        $adresse1 = $result[0]->getAdresseDepart() ;
        $adresse2= $result[0]->getAdresseArrivee() ;


        $kmTotal = $distance->apiCalcul($adresse1, $adresse2);

        //j'arrondis mon résultat distance
      $kmArrondie = floor($kmTotal);
      $price = $kmArrondie *2;

      if($price < 20){
          $price = 20;
      }

        return  $price;
    }

    function distance(CourseRepository $courseRepository, GetDistance $distance)
    {
        $result = $courseRepository->findBy([], ['id' => 'DESC'], 1);

        $adresse1 = $result[0]->getAdresseDepart() ;
        $adresse2= $result[0]->getAdresseArrivee() ;


        $kmTotal = $distance->apiCalcul($adresse1, $adresse2);

        //j'arrondis mon résultat distance
        $kmArrondie = floor($kmTotal);

        return  $kmArrondie;
    }
}