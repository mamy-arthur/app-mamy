<?php
namespace Common\Form\DataTransformer;

use Datetime;
use DateTimeImmutable;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToDateTimeTransformer implements DataTransformerInterface
{
    protected bool $immutable = false;

    public function __construct($options = [])
    {
        if (isset($options['immutable'])) {
            $this->immutable = $options['immutable'];
        }
    }

    /**
     * @param Datetime|DateTimeImmutable $dateTime The value to tranform
     *
     * @return string The passed dateTime param transformed to string
     */
    public function transform($dateTime): ?string
    { 
        return $dateTime ? $dateTime->format(DATE_RFC3339_EXTENDED) : NULL;
    }

    /**
     * @param string $dateTimeAsString The value to transform
     *
     * @return Datetime|DateTimeImmutable|null|false The passed string param transformed to object
     */
    public function reverseTransform($dateTimeAsString)
    {
        if ($this->immutable) {
            /** @var DateTimeImmutable|null|false */
            $value =  $dateTimeAsString? DateTimeImmutable::createFromFormat(DATE_RFC3339_EXTENDED, $dateTimeAsString) : null;
        } else {
            /** @var Datetime|null|false */
            $value =  $dateTimeAsString ? DateTime::createFromFormat(DATE_RFC3339_EXTENDED, $dateTimeAsString ) : null;
        }

        if ($value === false) {
            throw new TransformationFailedException("Could not convert string $dateTimeAsString to Datetime object.");
        }
        
        return $value;
    }
}
