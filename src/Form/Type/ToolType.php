<?php

namespace App\Form\Type;

use App\Entity\Tool;
use App\Repository\ToolsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToolType extends AbstractType
{
    /** @var ToolsRepository */
    private $toolsRepository;

    public function __construct(ToolsRepository $toolsRepository)
    {
        $this->toolsRepository = $toolsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['required' => true, 'attr' => ['class' => 'mb-3']])
            ->add('model', TextType::class, ['required' => true, 'attr' => ['class' => 'mb-3']])
            ->add(
                'code',
                TextType::class,
                [
                    'required' => true,
                    'attr'     => ['class' => 'mb-3'],
                    'data'     => $this->generateToolCode(),
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Tool::class,
            ]
        );
    }

    /**
     * Generate unique, random code of 6 digits
     */
    private function generateToolCode(): string
    {
        // TODO: find  out if it really needs to be random. Maybe padded row id from db can be used.
        do {
            $code = str_pad(intval(rand(1, 999999)), '6', '0', STR_PAD_LEFT);
        } while ($this->toolsRepository->findOneBy(['code' => $code]));

        return $code;
    }
}
