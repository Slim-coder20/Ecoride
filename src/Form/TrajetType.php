<?php

namespace App\Form;

use App\Entity\Trajet;
use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class TrajetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('depart',TextType::class, [
                'label' => 'Ville de départ'
                
            ])
            ->add('arrivee', textType::class, [
                'label' => "Ville d'arrivée"
            ])
            ->add('dateDepart',DateTimeType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text',
            ])
            ->add('dateArrivee',DateTimeType::class, [
                'label' => "Date d'arrivée",
                'widget' => 'single_text',
            ])
            ->add('placesRestantes', IntegerType::class, [
                'label' => 'Places disponibles'
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix (€)',
                'currency' => 'EUR'
            ])
          
            ->add('vehicule', EntityType::class, [
                'class' => Vehicule::class,
                'choice_label' => function (Vehicule $vehicule) {
                    return $vehicule->getBrand() . ' ' . $vehicule->getModel().' '.$vehicule->getLicensePlate();
                },
                'label' => 'Véhicule utilisé',
                'placeholder' => 'Choisir un véhicule',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trajet::class,
        ]);
    }
}
