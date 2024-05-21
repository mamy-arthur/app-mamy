<?php

namespace App\Repository;

use App\Entity\PermissionType;
use App\Helpers\PermissionTypeHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PermissionTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PermissionType::class);
    }

    /**
     * @param string $key
     * @return PermissionType
     */
    public function findOneByPermissionKey(string $key): ?object
    {
        $keyParts = PermissionTypeHelper::getPermissionKeyParts($key);

        return parent::findOneBy([
            'resourceType' => $keyParts['resourceType'],
            'resource' => $keyParts['resourceKey'],
        ]);
    }
}
