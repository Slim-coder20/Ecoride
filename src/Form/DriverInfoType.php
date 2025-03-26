<?php

namespace App\Form;

use App\Entity\Chauffeur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DriverInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('licensePlate', TextType::class, [
                'label' => "Plaque d'immatriculation",
            ])
            ->add('registrationDate', DateType::class, [
                'widget' => 'single_text',
                'label' => "Date de première immatriculation",
            ])
            ->add('brand', TextType::class, [
                'label' => 'Marque',
            ])
            ->add('model', TextType::class, [
                'label' => 'Modèle',
            ])
            ->add('color', TextType::class, [
                'label' => 'Couleur',
            ])
            ->add('seat', IntegerType::class, [
                'label' => 'Nombre de places disponibles',
            ])
            ->add('preferences', ChoiceType::class, [
                'label' => 'Préférences',
                'choices' => [
                    'Fumeur' => 'fumeur',
                    'Non-fumeur' => 'non_fumeur',
                    'Animal accepté' => 'animal',
                    "Pas d'animal" => 'no_animal',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('energie', TextType::class, [
                'label' => 'Énergie du véhicule',
            ])
            ->add('ecologique', ChoiceType::class, [
                'label' => 'Écologique',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
            ])
            ->add('photo', TextType::class, [
                'label' => 'Lien vers la photo du chauffeur',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chauffeur::class,
        ]);
    }
}
