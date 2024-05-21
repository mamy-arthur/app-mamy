<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="auth_api__roles")
 * @UniqueEntity("code")
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public ?int $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    public string $name;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    public string $code;

    /**
     * @ORM\ManyToMany(targetEntity="Permission", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinTable(name="auth_api__roles_permissions",
     *  joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id", unique=true)}
     * )
     */
    public Collection $permissions;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }

    public function addPermission(Permission $permission)
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
        }
    }

    public function removePermission(Permission $permission)
    {
        $this->permissions->removeElement($permission);
    }
}
