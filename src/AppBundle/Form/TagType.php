<?php


namespace AppBundle\Form;

use AppBundle\Entity\ToolTag;
use AppBundle\Entity\Tool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('tag');
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => ToolTag::class
        ]);
    }

}