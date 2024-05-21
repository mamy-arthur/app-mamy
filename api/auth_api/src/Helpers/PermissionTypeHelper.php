<?php

namespace App\Helpers;

use App\Enum\PlatformResourceEnum;

class PermissionTypeHelper {

    const RESOURCE_PARTS_SEPARATOR = ':';

    public static function getAvailablePermissionedResources(): array
    {
        return (new \ReflectionClass(PlatformResourceEnum::class))->getConstants();
    }

    /**
     * @param string $key
     * @return array with keys 'resourceType' and 'resourceKey'
     */
    public static function getPermissionKeyParts(string $key): array
    {
        $parts = explode(PermissionTypeHelper::RESOURCE_PARTS_SEPARATOR, $key);

        if (count($parts) != 2) {
            throw new \UnexpectedValueException('The provided key format is not valid!');
        }

        return [
            'resourceType' => $parts[0],
            'resourceKey' => $parts[1],
        ];
    }
}
