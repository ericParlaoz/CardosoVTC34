<?php

namespace App\Form;

use App\Entity\Course;

use Doctrine\DBAL\Types\TimeType;
use http\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints\Regex;

class CourseType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresseDepart', TextType::class, [
                'required' => true,
                'label' => "Adresse de départ",
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir une adresse départ"
                    ]),
                    new Regex ([
                        'pattern' => "/^[a-z0-9,.àâÀéèêÉÈÊçÇ,'_ -]+$/i",
                        'match' => true,
                        'message' => 'Les caractères spéciaux sont refusés'
                    ])
                ]
            ])
            ->add('adresseArrivee', TextType::class, [
                'required' => true,
                'label' => "Adresse d'arrivée",
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir une adresse d'arrivée"
                    ]),
                    new Regex ([
                        'pattern' => "/^[a-z0-9,.àâÀéèêÉÈÊçÇ,'_ -]+$/i",
                        'match' => true,
                        'message' => 'Les caractères spéciaux sont refusés'
                    ])
                ]
            ])
            ->add('passagers', NumberType::class, [
                'required' => true,
                'label' => "Nombre de passagers",
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez indiquer le nombre de passagers"
                    ]),
                    new Assert\Range([
                        'min' => 1, //passagers minimum
                        'max' => 5, //Passagers maximum
                        'notInRangeMessage' => "Le nombre de passagers doit être compris entre 1 et 5 !",
                    ]),
                ]
            ])
            ->add('date', DateTimeType::class, [
                'date_widget' => 'single_text',
                'minutes' => range(0,30,30), // Réservation uniquement toutes les 30 minutes
                'required' => true,
                'label' => "Date de réservation",
                'constraints' => [
                    new Assert\GreaterThan("+2 hours", message: "Veillez sélectionner une date de réservation correcte !")
                ] // Date de la course à partir de + 2heures de la date de saisie
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
