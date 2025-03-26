<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;



class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class,[
                'label' => 'Marque',
                'attr' => [
                    'placeholder' => 'Marque du véhicule'
                ]])

            ->add('model', TextType::class,[
                'label' => 'Modèle',
                'attr' => [
                    'placeholder' => 'Modèle du véhicule'
                ]])
            ->add('color', TextType::class,[
                'label' => 'Couleur',
                'attr' => [
                    'placeholder' => 'Couleur du véhicule'
                ]])
            ->add('licensePlate', TextType::class,[
                'label' => 'Immatriculation',
                'attr' => [
                    'placeholder' => 'Immatriculation du véhicule'
                ]])
            
            
            ->add('registrationDate',DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('seat', IntegerType::class,[
                'label' => 'Nombre de places',
                'attr' => [
                    'placeholder' => 'Nombre de places du véhicule'
                ]])
            
            ->add('energie', ChoiceType::class, [
                'choices' => [
                    'Essence' => 'Essence',
                    'Diesel' => 'Diesel',
                    'Electrique' => 'Electrique',
                    'Hybride' => 'Hybride',
                ],
            ])
            ->add('ecologique', CheckboxType::class, [
                'label' => 'Ecologique',
                'required' => false,
            ])
            
            ->add('photo', FileType::class, [
                'label' => 'Photo', 
                'mapped' => false,
                'required' => false,
            ])
         
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
