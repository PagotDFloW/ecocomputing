<?php

namespace App\Form;

use App\Entity\Produits;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'required' => true
            ])
            ->add('prix', null, [
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'required' => true
            ])
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
                'required' => false,
                'label' => "Image secondaire",
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
                'required' => false,
                'label' => "Image optionelle",
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            "image/png",
                            "image/jpeg"    
                        ]
                    ])
                ]
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
