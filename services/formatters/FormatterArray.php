<?php

declare(strict_types=1);

namespace FpDbTest\services\formatters;

use FpDbTest\exceptions\BadFormatException;
use FpDbTest\exceptions\BadParamTypeException;
use FpDbTest\services\FormatterFactory;

class FormatterArray implements FormatterInterface
{
    /**
     * @param array<int|string, string|int|float|bool|null> $value
     * @return string
     * @throws BadFormatException
     * @throws BadParamTypeException
     */
    public function format($value): string
    {
        $result = [];
        $formatterAny = FormatterFactory::getFormatterByType();
        foreach ($value as $key => $item) {
            $item = $formatterAny->format($item);
            if (is_string($key)) {
                $result[] = "`$key` = $item";
            } else {
                $result[] = $item;
            }
        }
        return implode(', ', $result);
    }

    /**
     * @param mixed $value
     * @return void
     * @throws BadParamTypeException
     * @throws BadFormatException
     */
    public function validate($value): void
    {
        if (!is_array($value)) {
            throw new BadParamTypeException();
        }
        $formatter = FormatterFactory::getFormatterByType();
        foreach ($value as $key => $item) {
            if (!is_string($key) && !is_int($key)) {
                throw new BadParamTypeException();
            }
            $formatter->validate($item);
        }
    }
}
