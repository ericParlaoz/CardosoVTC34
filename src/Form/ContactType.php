<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Les différents "add" sont les champs à renseigner dans le formulaire de contact
            // Ils contiennent chacun un tableau associatif avec un champ requis
           //  Une contrainte est effectué pour une vérification des champs coté serveur
            ->add('nom',TextType::class, [
                'required' => true,
                'label' => "Votre nom",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom'
                    ])
                ]
            ])
            ->add('email',EmailType::class, [
                'required' => true,
                'label' => "Votre adresse email",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre adresse émail'
                    ])
                ]
            ])
            ->add('telephone', NumberType::class, [
                'required' => true,
                'label' => "Téléphone",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre téléphone'
                    ]) ,
                    new Length([
                        'min' => 10,
                        'max'=>11,
                    ])
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => ['rows' => 6],
                'required' => true,
                'label' => "Votre message",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un message'
                    ])
                ]
            ])
            ->add('checkbox', CheckboxType::class, [
                'label'    => 'Confidentialité',
                'required' => true,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
