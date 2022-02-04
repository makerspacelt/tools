<?php

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class ToolUpdateType extends ToolType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            );
    }
}
