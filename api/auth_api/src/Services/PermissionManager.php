<?php

namespace App\Services;

use App\Entity\Permission;
use Common\Entity\AbstractEntityHandler;

class PermissionManager extends AbstractEntityHandler
{
    public function getEntityClassname(): string
    {
        return Permission::class;
    }
}
