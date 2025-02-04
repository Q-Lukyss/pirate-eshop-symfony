<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname',TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Prenom',
                    'required' => true,
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom',
                    'required' => true,
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
//                'constraints' => [],
                'attr' => [
                    'placeholder' => 'Email',
                    'required' => true,
                    ],
            ])
//            ->add('password', PasswordType::class, [
//                'label' => 'Mot de passe',
//                'attr' => [
//                    'placeholder' => 'Mot de passe',
//                    'required' => true,
//                ],
//            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length(['min' => 4, 'max' => 120] ),
                ],
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'hash_property_path' => 'password', // permet a symfony de savoir que ce champs correspond a User password
                    'attr' => [
                    'placeholder' => 'Mot de passe',
                ]],
                'second_options' => ['label' => 'Répétez Mot de passe', 'invalid_message' => 'qefdvbw', 'attr' => [
                    'placeholder' => 'Mot de passe',
                ]],
                'mapped' => false,
//                'invalid_message' => 'qefdvbw',
//                'error_bubbling' => true
            ])
            ->add('Inscription', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mx-auto',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'email',
                ])
            ]
        ]);
    }
}
