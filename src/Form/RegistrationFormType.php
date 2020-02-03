<?php

namespace App\Form;

use App\Entity\User;
use PhpParser\Node\Stmt\Label;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
            ])

            ->add('firstName', TextType::class, [
                // 'label' => 'Prénom'
            ])

            ->add('lastName', TextType::class, [
                // 'label' => 'Nom'
            ])

            // ->add('agreeTerms', CheckboxType::class, [
            //     'mapped' => false,
            //     'label' => 'Merci d\'accepter les conditions',
            //     'constraints' => [
            //         new IsTrue([
            //             'message' => 'Vous devez accepter les conditions',
            //         ]),
            //     ],
            // ])

            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Merci d\'indiquer des mots de passes identiques.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => [],
                'second_options'=> [],

            ]);
            // ->add('plainPassword', PasswordType::class, [
            //     // instead of being set onto the object directly,
            //     // this is read and encoded in the controller
            //     'mapped' => false,
            //     'label' => 'Mot de passe',
            //     'constraints' => [
            //         new NotBlank([
            //             'message' => 'Tapez un mot de passe svp',
            //         ]),
                    // new Length([
                    //     'min' => 6,
                    //     'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                    //     // max length allowed by Symfony for security reasons
                    //     'max' => 4096,
                    // ]),
            //     ],
            // ])

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
