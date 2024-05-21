<?php

namespace App\Services;

use App\Entity\Credentials;
use App\Form\CredentialsForm;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CredentialsFormManager
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function validateFormData(Request $request, Credentials $credentials): ?FormErrorIterator
    {
        $form = $this->formFactory->create(CredentialsForm::class, $credentials);

        $data = json_decode($request->getContent(), true);

        $form->submit($data);

        return $form->isSubmitted() && $form->isValid() ? NULL : $form->getErrors();
    }

}

