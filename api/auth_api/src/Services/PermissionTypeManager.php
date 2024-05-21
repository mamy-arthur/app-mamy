<?php

namespace App\Services;

use Common\Entity\AbstractEntityHandler;
use App\Entity\PermissionType;

class PermissionTypeManager extends AbstractEntityHandler
{

    public function getEntityClassname(): string
    {
        return PermissionType::class;
    }
}
