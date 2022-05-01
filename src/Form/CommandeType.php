<?php

namespace App\Form;

use App\Service\GetDistance;
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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


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
        // Je recupère les infos enregitrees dans la session pour les enregitrer dans des variables
        $adresse1 =$this->session->get('adresseDepart');
        $adresse2 =$this->session->get('adresseArrivee');
        $date = $this->session->get('date');

        // Je converti mon objet date en format "string" pour le futur enregistrement en bdd
        $dateConvertion = date_format($date, "d/m/Y/H:i");

        // Je fait appel à mon service de calcul et je les enregitre dans des variables
        $duree = $this->distance->apiCalculDuree($adresse1,$adresse2);
        $prix = $this->distance->apiCalculPrix($adresse1, $adresse2);

        // Je convertis les dates pour les champs
        date_default_timezone_set('Europe/Paris');
        $dateReservation = date('Y/m/d');
        $dateCompta= date( "Y");

        $builder
            ->add('nom', TextType::class,[
                'required' => true, // Le champs est requis
                'label' => "Votre nom",
                'constraints' => [
                    new NotBlank([ // Message en cas de champs vide
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
            ->add('adresse_depart', HiddenType::class, [ // champs hidden (non visible dans la vue)
                'data' => $adresse1, // j'ultilise les variables sur tous mes champs suivants
            ])
            ->add('adresse_arrivee', HiddenType::class, [
                'data' => $adresse2,
            ])
            ->add('date', HiddenType::class, [
                'data' => $dateConvertion,
            ])
            ->add('date_compta', HiddenType::class, [
                'data' => $dateCompta,
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
            'data_class' => null, // j'utilise le paramètre "null" car ce formulaire est destiné à deux entités
        ]);
    }
}
