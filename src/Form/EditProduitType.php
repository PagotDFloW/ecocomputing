<?php

namespace App\Form;

use App\Entity\Produits;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EditProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('prix')
            ->add('description', TextareaType::class)
            ->add('image1', FileType::class, [
                'mapped' => false,
                'required' => true,
                'label' => "Image Principale",
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            "image/png",
                            "image/jpeg"    
                        ]
                    ])
                ]
            ])
            ->add('image2', FileType::class, [
                'mapped' => false,
                'required' => true,
                'label' => "Image Principale",
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            "image/png",
                            "image/jpeg"    
                        ]
                    ])
                ]
            ])
            ->add('image3', FileType::class, [
                'mapped' => false,
                'required' => true,
                'label' => "Image Principale",
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            "image/png",
                            "image/jpeg"    
                        ]
                    ])
                ]
            ])
            ->add('produitCondition', ChoiceType::class, [
                'choices' => [
                    'neuf' => 'neuf',
                    'utilisé' => 'utilisé',
                    'vieux' => 'vieux',
                    'vinted' => 'vinted'
                ],
                'placeholder' => '-',
                'required' => false
            ])
            ->add('prixEstime')
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'original' => 'original',
                    'acheté' => 'acheté',
                    'en attente' => 'en attente',
                    'vendu' => 'vendu',
                    'refusé' => 'refusé'
                ],
                'placeholder' => '-',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
