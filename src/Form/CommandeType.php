<?php

namespace App\Form;

use App\Entity\Clients;
use App\Service\GetDistance;
use App\Service\UniqueIdService;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\ORM\Id\BigIntegerIdentityGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CommandeType extends AbstractType
{
    private $session;
    private $distance;

    public function __construct(SessionInterface $session, GetDistance $distance)
    {
        $this->session = $session;
        $this->distance =$distance;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $adresse1 =$this->session->get('adresseDepart');
        $adresse2 =$this->session->get('adresseArrivee');
        $duree = $this->distance->apiCalculDuree($adresse1,$adresse2);
        $prix = $this->distance->apiCalculPrix($adresse1, $adresse2);

        $date = $this->session->get('date');
        $dateConvertion = date_format($date, "d/m/Y/H:i");

        date_default_timezone_set('Europe/Paris');
        $dateReservation = date('d/m/Y');


        $builder
            ->add('nom', TextType::class,[
                'required' => true,
                'label' => "Votre nom",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom'
                    ])
                ]
            ])
            ->add('prenom', TextType::class ,[
                'required' => true,
                'label' => "Votre prénom",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre prénom'
                    ])
                ]
            ])
            ->add('entreprise', TextType::class,[
                'required' => false,
                'label' => "Entreprise (facultatif)",
            ])
            ->add('rue', TextType::class, [
                'required' => true,
                'label' => "Numéro et nom de rue",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre numéro et nom de rue'
                    ])
                ]
            ])
            ->add('codepostal', NumberType::class, [
                'required' => true,
                'label' => "Code postal",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre code postal'
                    ]),
                    new Length([
                        'min' => 4,
                        'max'=>5,
                        'maxMessage'=> 'Code postal non valide'
                    ])
                ]
            ])
            ->add('ville', TextType::class, [
                'required' => true,
                'label' => "Ville",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre ville'
                    ])
                ]
            ])
            ->add('telephone', TelType::class, [
                'required' => true,
                'label' => "Téléphone",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre téléphone'
                    ]) ,
                    new Length([
                        'min'=>10,
                        'max'=>10,
                    ])

                ]
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => "Adresse Email",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre adresse émail'
                    ])
                ]
            ])
            ->add('infos', TextareaType::class,[
                'required' => false,
                'label' => "Infos complémentaires (facultatif)",
            ])
            ->add('confidentialite', CheckboxType::class, [
                'label'    => 'Confidentialité',
                'required' => true,
            ])
            ->add('adresse_depart', HiddenType::class, [
                'data' => $adresse1,
            ])
            ->add('adresse_arrivee', HiddenType::class, [
                'data' => $adresse2,
            ])
            ->add('date', HiddenType::class, [
                'data' => $dateConvertion,
            ])
            ->add('unique_id', HiddenType::class, [
                'data' => $this->session->getId(),
            ])

            ->add('date_reservation', HiddenType::class, [
                'data' => $dateReservation,
            ])
            ->add('duree', HiddenType::class, [
                'data' => $duree,
            ])

            ->add('prix', HiddenType::class, [
                'data' => $prix,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Clients::class,
        ]);
    }
}
