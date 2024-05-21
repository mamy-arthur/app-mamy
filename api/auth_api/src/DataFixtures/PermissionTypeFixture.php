<?php

namespace App\DataFixtures;

use App\Entity\Permission;
use App\Entity\PermissionType;
use App\Entity\Role;
use App\Enum\ActionEnum as Action;
use App\Enum\PlatformResourceEnum as RES;
use App\Helpers\PermissionTypeHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;

const actions = 'actions';
const related = 'related';

const entity_actions = [
    Action::ACTION_VIEW,
    Action::ACTION_CREATE,
    Action::ACTION_DELETE,
    Action::ACTION_UPDATE,
];

const page_actions = [Action::ACTION_VIEW];

const permissionsMapping = [
    // todo: add all existing permission types here
    RES::FP__DASHBORD => [
        actions => page_actions,
    ],
    RES::FP__USER_ROLE_MANAGEMENT => [
        actions => page_actions,
    ],
    RES::ETY__USER => [
        actions => entity_actions,
    ],
    RES::ETY__ROLE => [
        actions => entity_actions,
    ]
];

class PermissionTypeFixture extends Fixture
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function load(ObjectManager $manager)
    {
        $adminRole = $manager
            ->getRepository(Role::class)
            ->findOneByCode('_SYSADM');

        /** @var \App\Repository\PermissionTypeRepository $repo */
        $repo = $manager->getRepository(PermissionType::class);

        foreach (array_keys(permissionsMapping) as $resourceKey) {
            $permissionType = $repo->findOneByPermissionKey($resourceKey);
            $addPermsToAdmin = !$permissionType;

            if (!$permissionType) {
                $resourceParts = explode(
                    PermissionTypeHelper::RESOURCE_PARTS_SEPARATOR,
                    $resourceKey,
                );

                $permissionType = new PermissionType();
                $permissionType->resourceType = $resourceParts[0];
                $permissionType->resource = $resourceParts[1];
                $permissionType->possiblesActions =
                    permissionsMapping[$resourceKey][actions];
            }

            if (!empty(permissionsMapping[$resourceKey][related])) {
                $permissionType->relatedPermTypes =
                    permissionsMapping[$resourceKey][related];
            }
            $manager->persist($permissionType);

            if ($addPermsToAdmin) {
                // todo: make it possible to update the already existing permissions
                $adminPermission = new Permission();
                $adminPermission->permissionType = $permissionType;
                $adminPermission->actions = $permissionType->possiblesActions;
                $adminRole->addPermission($adminPermission);

                $manager->persist($adminRole);
            }
        }

        $manager->flush();

        $this->logger->info('Fixture done.');
    }
}
