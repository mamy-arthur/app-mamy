<?php


namespace Common\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractEntityFieldMeta
 * @package Common\Entity
 * @ORM\MappedSuperclass()
 */
class AbstractEntityFieldMeta
{
    /**
     * @var string $dataType
     * @ORM\Column(type="string")
     */
    public string $dataType;

    /**
     * @var string $label
     * @ORM\Column(type="string")
     */
    public string $label;

    /**
     * @var string $name
     * @ORM\Column(type="string")
     */
    public string $name;

    /**
     * @var string|null $fieldType
     * @ORM\Column(type="string", nullable=true)
     */
    public ?string $fieldType;

    /**
     * @var string[]|null $objects
     * @ORM\Column(type="json", nullable=true)
     */
    public ?array $objects;

    /**
     * @var string[]|null $choices
     * @ORM\Column(type="json", nullable=true)
     */
    public ?array $choices;

    /**
     * @ORM\Column(type="integer", nullable=true, name="field_order")
     */
    public ?int $order;

    /**
     * @ORM\Column(type="boolean")
     */
    public ?bool $isRequired = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    public ?bool $isHidden = false;
}
