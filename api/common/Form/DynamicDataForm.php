<?php


namespace Common\Form;


use Common\Entity\AbstractEntityFieldMeta;
use Common\Enum\FieldMetaDataTypeEnum;
use Common\Enum\FieldMetaFieldTypeEnum;
use Common\Helper\DatetimeHelper;
use Common\Http\RequestHelper;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicDataForm extends CollectionType
{
    protected RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
    }

    public function onPostSubmit(FormEvent $event)
    {
        $this->validateData($event->getForm());
    }

    protected function validateData(FormInterface $form)
    {
        $data = $form->getData();

        /** @var AbstractEntityFieldMeta[] $fieldsMeta */
        $fieldsMeta = $form->getConfig()->getOption('fields_meta');

        foreach ($fieldsMeta as $field) {
            $value = $data[$field->name] ?? null;

            if ($field->isRequired && empty($value) && !($value === 0 || $value === '0')) {
                $form->addError(new FormError("The '$field->name' field is required!"));
            }

            $choicesMeta = $field->choices;

            if ($choicesMeta && !empty($value)) {
                $choices = $choicesMeta['values'] ?? $choicesMeta;

                if (is_string($choices)) {
                    $authHeader = $this->requestStack->getCurrentRequest()->headers->get('Authorization');
                    $apiToken=  $this->requestStack->getCurrentRequest()->headers->get('Api-Token');
                    $apiClient = $this->requestStack->getCurrentRequest()->headers->get('Api-Client');

                    $choices = RequestHelper::getUrlResponse($choices, [
                        'http' => [
                            'header' => 
                            "Authorization: $authHeader \r\n".
                            "Api-Token: $apiToken \r\n". 
                            "Api-Client: $apiClient",
                        ],
                    ]);

                    $choices = array_map(function ($plane) use ($choicesMeta) {
                        return $plane->{$choicesMeta['value_property']};
                    }, $choices);
                } elseif (is_array($choices)) {
                    $choices = array_keys($choices);
                }

                if ($field->fieldType == FieldMetaFieldTypeEnum::SELECT && !in_array($value, $choices)) {
                    $form->addError(new FormError("An incorrect value '$value' was provided for field '$field->name'."));
                }
                // todo: handle the multiple select case
            }

            $dataType = $field->dataType;

            if ($dataType === FieldMetaDataTypeEnum::DATETIME) {
                if($value && DatetimeHelper::getDatetimeFromString($value) === false) {
                    $form->addError(new FormError("An incorrect value '$value' was provided for field '$field->name'; it must be a valid ISO 8601 date/time string."));
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('fields_meta');

        $resolver->setDefaults([
            'csrf_protection' => false,
            'entry_type' => TextType::class,
            'allow_add' => true
        ]);

        $resolver->setAllowedTypes('fields_meta', 'array');
    }
}
