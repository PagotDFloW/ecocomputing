<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'required' =>true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un email',
                    ])
                ]
            ])
            ->add('nom', TextType::class, [
                'required' =>true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom',
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'required' =>true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un prénom',
                    ])
                ]
            ])
            ->add('phone', TelType::class, [
                'required' =>true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un prénom',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Numéro trop court',
                        // max length allowed by Symfony for security reasons
                        'max' => 10,
                    ]),
                ]
            ])
            ->add('plainPassword', RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'required' =>true,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
