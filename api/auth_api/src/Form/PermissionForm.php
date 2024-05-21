<?php

namespace App\Form;

use App\Entity\Permission;
use App\Entity\PermissionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actions', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true
            ])
            ->add('permission_type', EntityType::class, [
                    'class' => PermissionType::class,
                    'choice_label' => 'resource',
                    'property_path' => 'permissionType'

                ]
            )
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                [$this, 'onPostSubmit']
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Permission::class,
            'csrf_protection' => false
        ));
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $possiblesActions = $event->getData()->permissionType->possiblesActions;
        $actions = $event->getData()->actions;
        $actions = array_filter($actions);

        if ($invalid = array_diff($actions, $possiblesActions)) {
            $invalidString = implode(', ', $invalid);
            $event->getForm()->get('actions')->addError(new FormError("Invalid actions provided! => $invalidString"));
        }

    }
}
