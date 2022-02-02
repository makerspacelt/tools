<?php

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class ToolUpdateType extends ToolType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'photos',
                CollectionType::class,
                [
                    'required'     => false,
                    'entry_type'   => ToolPhotoType::class,
                    'allow_add'    => false,
                    'allow_delete' => true,
                    'label'        => false,
                    'by_reference' => false,
                ]
            )
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
                ]
            );
    }
}
