<?php

namespace App\Services;

use App\Form\UploadedFileForm;
use Common\Form\BaseFormManager;

class UploadedFileFormManager extends BaseFormManager
{

    public function getFormClassname(): string
    {
        return UploadedFileForm::class;
    }
}
