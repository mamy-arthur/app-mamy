<?php

namespace App\Form;

use App\Entity\Credentials;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CredentialsForm extends AbstractType
{
    protected FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('username', TextType::class)
            ->add('password', TextType::class, [
                'property_path' => 'passwordPlain',
            ])
            ->add('roles', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => Role::class,
                ],
                'allow_add' => true,
                'allow_delete' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Credentials::class,
            'csrf_protection' => false,
        ]);
    }
}
