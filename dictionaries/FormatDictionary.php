<?php

declare(strict_types=1);

namespace FpDbTest\dictionaries;

final class FormatDictionary
{
    public const string ANY = '?';
    public const string INTEGER = '?d';
    public const string FLOAT = '?f';
    public const string ARRAY = '?a';
    public const string IDENTITY = '?#';

    public static function getSpecialTypes(): array
    {
        return [
            self::INTEGER,
            self::FLOAT,
            self::ARRAY,
            self::IDENTITY,
        ];
    }

    public static function getTypes(): array
    {
        return [
            self::ANY,
            self::INTEGER,
            self::FLOAT,
            self::ARRAY,
            self::IDENTITY,
        ];
    }
}
