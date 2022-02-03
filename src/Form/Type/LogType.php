<?php

namespace App\Form\Type;

use App\Entity\ToolLog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add(
            'log',
            TextareaType::class,
            [
                'label' => false,
            ]
        )
        ->add('type', ChoiceType::class,
            ['choices' => ['Fixed' => 'LOG_TYPE_FIXED', 'Broken' => 'LOG_TYPE_BROKEN', 'In progress' => 'LOG_TYPE_INPROGRESS'], 'attr' => ['class' => 'mb-3']]
        );;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => ToolLog::class,
            ]
        );
    }
}
