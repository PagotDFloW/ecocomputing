<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('date')
            ->add('avis', TextareaType::class, [
                "label" => "Avis : ", 
                "attr" => ["class" => "form-group w100", 
                "rows" => "7"]
            ])
            ->add('Envoyer', SubmitType::class, [
                "attr" => ["class" => "center btnsubmitsaveavis"] 
            ]); 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
