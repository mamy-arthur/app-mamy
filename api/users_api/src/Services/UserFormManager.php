<?php

namespace App\Services;

use App\Entity\User;
use App\Form\UserForm;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class UserFormManager
{

    protected FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function validateUser(Request $request, User $user): ?FormErrorIterator
    {
        $form = $this->formFactory->create(UserForm::class, $user);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            return null;
        }
        return $form->getErrors();
    }
}

