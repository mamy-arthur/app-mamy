<?php

namespace Common\DTO;

use Common\Enum\ListFetchingFilterEnum;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\OutOfBoundsException;

/**
 * @property string $type
 */
class ListFetchingFilterDTO extends BaseDTO
{
    public function __construct(array $data = [])
    {
        if ($type = $data['type']) {
            $this->checkType($type);
        }

        parent::__construct($data);
    }

    protected string $type;
    public array $values;

    public function __set(string $field, $value)
    {
        if ($field === 'type') {
            $this->checkType($value);

            $this->type = $value;
        }
    }

    public function __get(string $field)
    {
        if ($field === 'type') {
            return $this->type;
        } else {
            $class = static::class;
            throw new OutOfBoundsException("The '$field' field is not defined on the '$class' class");
        }
    }

    protected function checkType($value)
    {
        if (!in_array($value, (new \ReflectionClass(ListFetchingFilterEnum::class))->getConstants())) {
            throw new InvalidArgumentException("'$value' is not a valid filter type");
        }
    }
}
