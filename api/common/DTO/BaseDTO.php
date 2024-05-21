<?php


namespace Common\DTO;


class BaseDTO
{
    public function __construct(array $data = [])
    {
        foreach (array_keys($data) as $fieldName) {
            if (array_key_exists($fieldName, get_class_vars(static::class))) {
                $this->$fieldName = $data[$fieldName];
            }
        }
    }
}
