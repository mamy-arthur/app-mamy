<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\PermissionType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity()
 * @ORM\Table(name="auth_api__permissions")
 */
class Permission
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public ?int $id;

    /**
     * @ORM\Column(type="simple_array")
     */
    public array $actions;

    /**
     * @ORM\ManyToOne(targetEntity="PermissionType")
     * @ORM\JoinColumn(name="permission_type_id", referencedColumnName="id")
     */
    public PermissionType $permissionType;
    
}