<?php

namespace App\Services;

use App\Form\EmailingForm;
use Common\Form\BaseFormManager;

class EmailingFormManager extends BaseFormManager
{
    public function getFormClassname(): string
    {
        return EmailingForm::class;
    }
}
