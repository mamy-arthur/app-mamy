<?php

namespace Common\Form;

use Common\Enum\FieldMetaDataTypeEnum;
use Common\Enum\FieldMetaFieldTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BaseFieldMetaForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('data_type', ChoiceType::class, [
                'property_path' => 'dataType',
                'choices' => (new \ReflectionClass(FieldMetaDataTypeEnum::class))->getConstants()
            ])
            ->add('label', TextType::class)
            ->add('order', NumberType::class)
            ->add('name', TextType::class)
            ->add('is_required', CheckboxType::class, ['property_path' => 'isRequired'])
            ->add('field_type', ChoiceType::class, [
                'property_path' => 'fieldType',
                'choices' => (new \ReflectionClass(FieldMetaFieldTypeEnum::class))->getConstants()
            ])
            ->add('objects', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true
            ])
            ->add('choices', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true
            ]);
    }
}
