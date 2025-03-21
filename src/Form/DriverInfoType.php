<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class DriverInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isChauffeur', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Êtes-vous chauffeur ?',
            ])
            ->add('licensePlate', TextType::class, [
                'required' => false,
                'label' => "Plaque d'immatriculation",
            ])
            ->add('registrationDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date de première immatriculation',
            ])
            ->add('model', TextType::class, [
                'required' => false,
                'label' => 'Modèle du véhicule',
            ])
            ->add('color', TextType::class, [
                'required' => false,
                'label' => 'Couleur du véhicule',
            ])
            ->add('brand', TextType::class, [
                'required' => false,
                'label' => 'Marque du véhicule',
            ])
            ->add('seat', IntegerType::class, [
                'required' => false,
                'label' => 'Nombre de places disponibles',
            ])
            ->add('preferences', ChoiceType::class, [
                'choices' => [
                    'Fumeur' => 'fumeur',
                    'Non-fumeur' => 'non_fumeur',
                    'Animal accepté' => 'animal',
                    "Pas d'animal" => 'no_animal',
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'label' => 'Préférences',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
