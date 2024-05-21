<?php

namespace App\Services;

use App\Entity\Role;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RoleForm;
use Symfony\Component\Form\FormErrorIterator;

class RoleFormManager{

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }
    
    public function validateRole(Request $request, Role $role): ?FormErrorIterator
    {
        $form = $this->formFactory->create(RoleForm::class, $role);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            return null;
        }
        return $form->getErrors();
    }

}

