<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('current_password', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length(['min' => 4, 'max' => 120] ),
                ],
                'first_options'  => [
                    'label' => 'Nouveau Mot de passe',
                    'hash_property_path' => 'password', // permet a symfony de savoir que ce champs correspond a User password
                    ],
                'second_options' => [
                    'label' => 'Confirmer Mot de passe',
                    'invalid_message' => 'Les mots de passe ne correspondent pas.',
                  ],
                'mapped' => false,
            ])
            ->add('Modifier', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mx-auto',
                ]
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {

                $form = $event->getForm();
                $user = $form->getConfig()->getOptions()['data'];

                // on le passe du controleur au formulaire pour l'utiliser
                $password_hasheur = $form->getConfig()->getOptions()['password_hasheur'];

                $is_valid = $password_hasheur->isPasswordValid(
                    $user,
                    $form->get('current_password')->getData(),
                );
                if (!$is_valid) {
                    // Envoyer une erreur sur le champs
                    $form->get('current_password')->addError(new FormError('Mot de passe incorrect'));
                }

            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'password_hasheur' => null, // besoin de le mettre a null sinon error
        ]);
    }
}
