<?php


namespace Common\Helper;


use DateTime;
use DateTimeImmutable;

class DatetimeHelper
{
    static function getDatetimeFromString($dateTimeAsString, $immutable = false, $format = DATE_RFC3339_EXTENDED)
    {
        if ($immutable) {
            /** @var DateTimeImmutable|null|false */
            $output =  $dateTimeAsString ? DateTimeImmutable::createFromFormat($format, $dateTimeAsString) : null;
        } else {
            /** @var Datetime|null|false */
            $output =  $dateTimeAsString ? DateTime::createFromFormat($format, $dateTimeAsString ) : null;
        }

        return $output;
    }
}
