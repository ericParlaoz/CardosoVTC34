<?php

namespace App\Form;

use App\Entity\Clients;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\ORM\Id\BigIntegerIdentityGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
            ->add('codepostal', TextType::class, [
                'required' => true,
                'label' => "Code postal",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre code postal'
                    ]),
                    new Length([
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
            ->add('telephone', TextType::class, [
                'required' => true,
                'label' => "Téléphone",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre téléphone'
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

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Clients::class,
        ]);
    }
}
