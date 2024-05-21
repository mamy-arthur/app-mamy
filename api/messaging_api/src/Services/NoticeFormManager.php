<?php

namespace App\Services;

use App\Form\NoticeForm;
use Common\Form\BaseFormManager;

class NoticeFormManager extends BaseFormManager
{
    public function getFormClassname(): string
    {
        return NoticeForm::class;
    }
}
