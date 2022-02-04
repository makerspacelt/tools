<?php

namespace App\Form\Type;

use App\Entity\Tool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\ToolLog;
use App\Entity\ToolParameter;

class ToolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['required' => true, 'attr' => ['class' => 'mb-3']])
            ->add('model', TextType::class, ['required' => true, 'attr' => ['class' => 'mb-3']])
            ->add(
                'code',
                TextType::class,
                [
                    'required'    => true,
                    'attr'        => ['class' => 'mb-3'],
                ]
            )
            ->add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'mb-3']])
            ->add('tags', TagType::class, ['required' => false, 'attr' => ['class' => 'mb-3']])
            ->add(
                'params',
                CollectionType::class,
                [
                    'required'     => false,
                    'entry_type'   => ParamType::class,
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'label'        => false,
                    'by_reference' => false,
                    'prototype' => true,
                    'prototype_data' => new ToolParameter(),
                    'prototype_name' => '_newparameteritem_'
                ]
            )
            ->add(
                'logs',
                CollectionType::class,
                [
                    'required'     => false,
                    'entry_type'   => LogType::class,
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'label'        => false,
                    'by_reference' => false,
                    'prototype' => true,
                    'prototype_data' => new ToolLog(),
                    'prototype_name' => '_newlogitem_'
                ]
            )
            ->add(
                'instructionsPdf',
                FileType::class,
                [
                    'mapped' => false,
                    'multiple' => false,
                    'required' => false,
                    'constraints' => [
                        new File(
                            [
                                'mimeTypes' => [
                                    'application/pdf',
                                ],
                            ]
                        ),
                    ]
                ]
            )
            ->add(
                'new_photos',
                FileType::class,
                [
                    'mapped'      => false,
                    'multiple'    => true,
                    'required'    => false,
                    'constraints' => [
                        new All(
                            [
                                new File(
                                    [
                                        'mimeTypes' => [
                                            'image/bmp',
                                            'image/jpeg',
                                            'image/png',
                                        ],
                                    ]
                                ),
                            ]
                        ),
                    ],
                ]
            )
            ->add('shoplinks', TextareaType::class, ['required' => false, 'label' => 'Where to buy?'])
            ->add('originalprice', TextType::class, ['required' => false, 'label' => 'Original price'])
            ->add(
                'acquisitiondate',
                DateType::class,
                ['required' => false, 'widget' => 'single_text', 'label' => 'Acquisition date']
            )
            ->add('save', SubmitType::class, ['label' => 'Submit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Tool::class,
            ]
        );
    }
}
