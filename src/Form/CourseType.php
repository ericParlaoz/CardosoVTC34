<?php

namespace App\Form;

use App\Entity\Course;

use Doctrine\DBAL\Types\TimeType;
use http\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
                    ])
                ]
            ])
            ->add('adresseArrivee', TextType::class, [
                'required' => true,
                'label' => "Adresse d'arrivée",
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez saisir une adresse d'arrivée"
                    ])
                ]
            ])
            ->add('passagers', IntegerType::class, [
                'required' => true,
                'attr' => [
                    'min'=>1,
                    'max' =>5,
                ],
                'label' => "Nombre de passagers",
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez indiquer le nombre de passagers"
                    ]),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => "Le nombre de passager doit être compris entre 1 et 5 !",
                    ]),
                ]
            ])
            ->add('date', DateTimeType::class, [
                'date_widget' => 'single_text',
                'minutes' => range(0,30,30),
                'required' => true,
               // 'html5'=>false,
              //  'format' => 'dd/MM/yyyy',
                'label' => "Date de réservation",
                'constraints' => [
                    new Assert\GreaterThan("+1 hours", message: "Veillez sélectionner une date supérieure !")

                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
