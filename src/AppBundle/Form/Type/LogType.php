<?php


namespace AppBundle\Form\Type;


use AppBundle\Entity\ToolLog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('log', TextareaType::class, ['label' => false]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => ToolLog::class
        ]);
    }


}