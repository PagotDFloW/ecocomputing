<?php

namespace App\Form;

use App\Entity\Commandes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse')
            ->add('ville')
            ->add('codePostal')
            ->add('paiement', ChoiceType::class, [
                'required' => true,
                'choices'=> [
                    'Visa' => 'Visa',
                    'Mastercard' => 'Mastercard',
                    'Paypal' => 'Paypal'
                ],
                'expanded' => true
            ])
            ->add('livraison', ChoiceType::class, [
                'required' => true,
                'data' => 'Standard',
                'choices' => [
                    'Standard' => 'Standard',
                    'Express' => 'Express',
                    'Relais Colis' => 'Relais Colis'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commandes::class,
        ]);
    }
}
