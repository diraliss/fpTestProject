<?php

declare(strict_types=1);

namespace FpDbTest\services;

use FpDbTest\dictionaries\FormatDictionary;
use FpDbTest\exceptions\BadFormatException;
use FpDbTest\services\formatters\FormatterAny;
use FpDbTest\services\formatters\FormatterArray;
use FpDbTest\services\formatters\FormatterFloat;
use FpDbTest\services\formatters\FormatterIdentity;
use FpDbTest\services\formatters\FormatterInteger;
use FpDbTest\services\formatters\FormatterInterface;

class FormatterFactory
{
    /**
     * @throws BadFormatException
     */
    public static function getFormatterByType(string $type = FormatDictionary::ANY): FormatterInterface
    {
        switch ($type) {
            case FormatDictionary::ANY:
                return new FormatterAny();
            case FormatDictionary::INTEGER:
                return new FormatterInteger();
            case FormatDictionary::FLOAT:
                return new FormatterFloat();
            case FormatDictionary::ARRAY:
                return new FormatterArray();
            case FormatDictionary::IDENTITY:
                return new FormatterIdentity();
        }
        throw new BadFormatException();
    }
}
