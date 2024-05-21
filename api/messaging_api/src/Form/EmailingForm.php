<?php

namespace App\Form;

use App\Entity\Emailing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailingForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('to_user', TextType::class, ['property_path' => 'toUser'])
            ->add('subject', TextType::class)
            ->add('content', TextType::class)
            ->add('file', FileType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Emailing::class,
            'csrf_protection' => false
        ]);
    }
}
