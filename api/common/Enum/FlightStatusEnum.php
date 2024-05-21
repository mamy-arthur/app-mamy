<?php

namespace Common\Enum;

abstract class FlightStatusEnum
{
    const PLANNED = 'planned';
    const CANCELLED = 'cancelled';
    const PENDING = 'pending';
    const LANDED = 'landed';
    const DELAYED = 'delayed';
    const APPROACHING = 'approaching';
    const TAKEN_OFF = 'taken_off';
}
