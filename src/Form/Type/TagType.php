<?php

namespace App\Form\Type;

use App\Form\DataTransformer\TagTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TagType extends AbstractType
{
    /** @var TagTransformer */
    private $tagTransformer;

    public function __construct(TagTransformer $tagTransformer)
    {
        $this->tagTransformer = $tagTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addModelTransformer($this->tagTransformer)
            ->add(
                'tags',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'tagsinput',
                    ],
                ]
            );
    }
}
