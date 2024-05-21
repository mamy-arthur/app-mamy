<?php

namespace Common\Enum;

abstract class ListFetchingFilterEnum
{
    const IS_TIME_BETWEEN = 'isTimeBetween';
    const IS_TIME_BETWEEN_OR_IS_NULL = 'isTimeBetweenOrIsNull';
    const TEXT_INCLUDES_SOME = 'textIncludesSome';
    const EQUALS = 'Equals';
    const TEXT_EQUALS = 'textEquals';
    const TEXT_INCLUDES_SOME_OR_IS_NULL = 'textIncludesSomeOrIsNull';
}
