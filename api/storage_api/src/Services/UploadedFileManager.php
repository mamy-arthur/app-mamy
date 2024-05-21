<?php

namespace App\Services;

use App\Entity\UploadedFile;
use Common\Entity\AbstractEntityHandler;

class UploadedFileManager extends AbstractEntityHandler
{
    public function getEntityClassname(): string
    {
        return UploadedFile::class;
    }
}
