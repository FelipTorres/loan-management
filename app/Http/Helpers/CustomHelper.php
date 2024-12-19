<?php

namespace App\Http\Helpers;

class CustomHelper
{
    public static function applyTrim(string $value): string
    {
        return trim($value);
    }

    public static function cleanValues(array $item): array
    {
        return array_map(function($value) {

            return trim($value, "\r\n");

        }, $item);
    }
}
