<?php

namespace App\Form;

use App\Entity\Adress;
use App\Entity\Carrier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addresses', EntityType::class, [
                'class' => Adress::class,
                'label' => 'Choix de l\'adresse de livraison',
                'required' => true,
                'expanded' => true,
                'label_html' => true,
                'choices' => $options['addresses']
            ])

            ->add('carriers', EntityType::class, [
                'class' => Carrier::class,
                'label' => 'Choix du Tranporteur',
                'required' => true,
                'expanded' => true,
                'label_html' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Commander',
                'attr' => [
                    'class' => 'btn btn-outline-success',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // déclarer variavble a null pour pas d'erreir
            'addresses' => null,
        ]);
    }
}
