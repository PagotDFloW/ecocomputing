<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('date')
            ->add('Nom', TextType::class, [
                "label" => "Nom : "
            ])
            ->add('Prenom', TextType::class, [
                "label" => "Prénom : "
            ])
            ->add('Mail', EmailType::class, [
                "label" => "Adresse mail : "
            ])
            ->add('Sujet', TextType::class, [
                "label" => "Sujet: "
            ])
            ->add('Message', TextareaType::class, [
                "label" => "Votre message : ",
                "attr" => ["class" => "form-group w100",
                    "rows" => "7"]
            ])
            ->add('Envoyer', SubmitType::class, [
                "attr" => ["class" => "center btnsubmit"]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
