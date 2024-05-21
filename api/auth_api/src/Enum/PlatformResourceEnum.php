<?php

namespace App\Enum;

use App\Helpers\PermissionTypeHelper;

const front_page = RessourceTypeEnum::FRONT_PAGE;
const entity = RessourceTypeEnum::ENTITY;
const separator = PermissionTypeHelper::RESOURCE_PARTS_SEPARATOR;

abstract class PlatformResourceEnum
{
    const FP__USER_ROLE_MANAGEMENT =
        front_page . separator . 'users-roles-management';
    const ETY__USER = entity . separator . 'user';
    const ETY__ROLE = entity . separator . 'role';

    const FP__DASHBORD = front_page . separator . 'dashboard';
}
