<?php

namespace App\Form;

use App\Entity\Notice;
use Common\Form\DataTransformer\StringToDateTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoticeForm extends AbstractType
{
    protected StringToDateTimeTransformer $stringToDateTimeTransformer;

    public function __construct()
    {
        $this->stringToDateTimeTransformer = new StringToDateTimeTransformer([
            'immutable' => true,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', TextType::class)
            ->add('start_date', TextType::class, array('property_path' => 'startDate'))
            ->add('end_date', TextType::class, array('property_path' => 'endDate'))
            ->add('type', TextType::class);

        $builder->get('start_date')
            ->addModelTransformer($this->stringToDateTimeTransformer);

        $builder->get('end_date')
            ->addModelTransformer(
                $this->stringToDateTimeTransformer
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Notice::class,
            'csrf_protection' => false
        ));
    }
}
