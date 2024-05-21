<?php


namespace Common\Form;

use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BaseFormManager
 * @package Common\Form
 * @template E
 */
abstract class BaseFormManager
{
    protected FormFactoryInterface $formFactory;

    protected bool $handlesJsonData = true;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param Request $request
     * @param $data
     * @return FormErrorIterator|null
     */
    public function validateForm(Request $request, $data, $formOptions = []): ?FormErrorIterator
    {
        $form = $this->getFormInstance($data, $formOptions);

        $form->submit(in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) ? array_merge($request->request->all(), $request->files->all()) : $request->query->all());

        return $form->isSubmitted() && $form->isValid() ? null : $form->getErrors();
    }

    /**
     * @param null $data
     * @return FormInterface
     */
    public function getFormInstance($data = null, $formOptions = []): FormInterface
    {
        return $this->formFactory->create($this->getFormClassname(), $data, $formOptions);
    }

    abstract public function getFormClassname(): string;
}
