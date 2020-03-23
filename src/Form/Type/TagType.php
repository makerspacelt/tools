<?php

namespace App\Form\Type;

use App\Form\DataTransformer\TagTransformer;
use App\Repository\TagsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TagType extends AbstractType
{
    /** @var TagsRepository */
    private $tagsRepository;

    public function __construct(TagsRepository $tagsRepository)
    {
        $this->tagsRepository = $tagsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'tags',
            TextType::class,
            [
                'attr' => [
                    'class' => 'tagsinput',
                ],
            ]
        )
            ->addModelTransformer(new TagTransformer($this->tagsRepository));
    }

}
