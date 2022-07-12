<?php

namespace App\Form;

use App\Entity\Prestations;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PrestationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Changement de carte mère' => 'Changement de carte mère',
                    'Chanegement/ajout de barrettes mémoires' => 'Chanegement/ajout de barrettes mémoires',
                    'Changement/ajout de disque dur' => 'Changement/ajout de disque dur',
                    'Changement d\'écran' => 'Changement d\'écran',
                    'Remplacement de clavier' => 'Remplacement de clavier'
                ],
                'placeholder' => '-'
            ])
            // ->add('parts')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prestations::class,
        ]);
    }
}
