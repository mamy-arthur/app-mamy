<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PermissionTypeRepository")
 * @ORM\Table(name="auth_api__permissions_types",
 *  uniqueConstraints={
 *        @ORM\UniqueConstraint(name="resource_unique",
 *            columns={"resource_type", "resource"})
 *    }
 * )
 * @UniqueEntity(
 * fields={"resourceType", "resource"},
 * )
 */
class PermissionType
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
    public string $resourceType;

    /**
     * @ORM\Column(type="string", nullable = true)
     */
    public ?string $resource;

    /**
     * @ORM\Column(type="simple_array")
     */
    public array $possiblesActions;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    public array $relatedPermTypes = [];
}
