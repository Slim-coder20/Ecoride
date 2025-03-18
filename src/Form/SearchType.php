<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('departure', TextType::class, [
                'label' => 'Départ',
                'attr' => [
                    'placeholder' => 'Saisir une ville de départ'
                ]
            ])
            ->add('destination', TextType::class, [
                'label' => 'Destination',
                'attr' => [
                    'placeholder' => 'Saisir une ville de destination'
                ]
            ])
            ->add('date', DateType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Sélectionner une date de départ'
                ]
            ])
            ->add('passengers', IntegerType::class, [
                'label' => 'Nombre de passagers',
                'attr' => [
                    'placeholder' => 'Saisir le nombre de passagers'
                ]
            ])
            ->add('smokers', ChoiceType::class, [
                'choices' => [
                    'Indifférent' => 'indifferent',
                    'Oui' => 'yes',
                    'Non' => 'no'
                ],
                'label' => 'Fumeurs',
            ])
            ->add('animals', ChoiceType::class, [
                'choices' => [
                    'Indifférent' => 'indifferent',
                    'Oui' => 'yes',
                    'Non' => 'no',
                ],
                'label' => 'Animaux',
            ])
            ->add('fuelType', ChoiceType::class, [
                'choices' => [
                    'Tous' => 'all',
                    'Essence' => 'petrol',
                    'Diesel' => 'diesel',
                    'Électrique' => 'electric',
                    'Hybride' => 'hybrid',
                ],
                'label' => 'Type de carburant',
            ])
            ->add('vehicleType', ChoiceType::class, [
                'choices' => [
                    'Indifférent' => 'indifferent',
                    'Voiture' => 'car',
                    'Moto' => 'motorcycle',
                    'Vélo' => 'bicycle',
                ],
                'label' => 'Type de véhicule',
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'btn-success',
                ]
                
            ])
            ->add('reset', SubmitType::class, [
                'label' => 'Réinitialiser',
                'attr' => [
                    'class' => 'btn-suceess',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
