<?php


namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\TagTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TagType extends AbstractType {

    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add(
            'tags', TextType::class, ['attr' => ['class' => 'tagsinput']]
        )->addModelTransformer(new TagTransformer($this->em));
    }

}