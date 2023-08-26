<?php

namespace App\Helpers;

class StringHelper
{
    public static function allArrayKeysAreString(array $array)
    {
        return self::hasStringKeys($array, count($array));
    }

    public static function hasStringKeys(array $array, int $stringKeysAmount = 1)
    {
        return count(array_filter(array_keys($array), 'is_string')) >= $stringKeysAmount;
    }
}
