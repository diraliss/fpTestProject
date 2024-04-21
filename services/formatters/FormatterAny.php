<?php

declare(strict_types=1);

namespace FpDbTest\services\formatters;

use FpDbTest\exceptions\BadParamTypeException;

class FormatterAny implements FormatterInterface
{
    /**
     * @param string|int|float|bool|null $value
     * @return string
     * @throws BadParamTypeException
     */
    public function format($value): string
    {
        if (is_string($value)) {
            return "'$value'";
        } elseif (is_int($value) || is_float($value)) {
            return "$value";
        } elseif (is_bool($value)) {
            $value = (int) $value;
            return "$value";
        } elseif (is_null($value)) {
            return 'NULL';
        }
        throw new BadParamTypeException();
    }

    /**
     * @param mixed $value
     * @return void
     * @throws BadParamTypeException
     */
    public function validate(mixed $value): void
    {
        if (
            !is_string($value) && !is_null($value) && !is_bool($value)
            && !is_int($value) && !is_float($value)) {
            throw new BadParamTypeException();
        }
    }
}
