<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\User;
use App\Services\AuthManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserForm extends AbstractType
{
    protected AuthManager $userRoleManager;

    public function __construct(AuthManager $userRoleManager)
    {
        $this->userRoleManager = $userRoleManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, array('property_path' => 'firstName'))
            ->add('last_name', TextType::class, array('property_path' => 'lastName'))
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
            ])
            ->add('email', EmailType::class)
            ->add('mobile_number', TelType::class, array('property_path' => 'mobileNumber'))
            ->add('address', TextType::class)
            ->add('registration_number', TextType::class, array('property_path' => 'registrationNumber'))
            ->add('roles', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'mapped' => false,
            ])
            ->add('is_active', CheckboxType::class, ['property_path' => 'isActive'])
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                [$this, 'onPreSubmit'] // todo: change to postSubmit
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'csrf_protection' => false
        ));
    }

    public function onPreSubmit(FormEvent $event): void
    {
        $this->checkRoles($event);
    }

    public function checkRoles(FormEvent $event)
    {
        $validRolesCodes = array_map(
            function ($role) {
                return $role->code;
            }
            , $this->userRoleManager->getRoles()
        );

        $rolesCodes = $event->getData()['roles'];

        if ($invalid = array_diff($rolesCodes, $validRolesCodes)) {
            $invalidString = implode(', ', $invalid);
            $event->getForm()->get('roles')->addError(new FormError("Invalid role(s) provided! => $invalidString"));
        }
    }
}
