<?php

namespace App\Services;

use App\Entity\Credentials;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Common\Entity\AbstractEntityHandler;
use Doctrine\Common\Collections\ArrayCollection;

class RoleManager extends AbstractEntityHandler
{
    private  PermissionManager $permissionManager;
    public function __construct(EntityManagerInterface $entityManager, PermissionManager $permissionManager)
    {
        parent::__construct($entityManager);
        $this->permissionManager = $permissionManager;
    }
    public function getRoles(string $username = null): array
    {
        /** @var Role[]|string[] $output */
        $output = [];

        if ($username) {
            /** @var Credentials $user */
            $user = $this->entityManager->getRepository(Credentials::class)->findOneByUsername($username);

            if ($user) {
                $output = $user->getRoles();
            }
        } else {
            $output = $this->entityManager->getRepository(Role::class)->findAll();
        }

        return $output;
    }

    public function getEntityClassname(): string
    {
        return Role::class;
    }

    /**
     * @param Role $role
     */
    public function save($role)
    {
        $this->entityManager->persist($role);
        $this->entityManager->flush();
    }


}
