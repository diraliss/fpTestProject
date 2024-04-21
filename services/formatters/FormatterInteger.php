<?php

declare(strict_types=1);

namespace FpDbTest\services\formatters;

use FpDbTest\exceptions\BadParamTypeException;

class FormatterInteger implements FormatterInterface
{
    /**
     * @param string|int|float|bool|null $value
     * @return string
     * @throws BadParamTypeException
     */
    public function format($value): string
    {
        if (is_string($value) || is_float($value)) {
            $value = intval($value);
            return "$value";
        } elseif (is_int($value)) {
            return "$value";
        } elseif (is_null($value)) {
            return 'NULL';
        } elseif (is_bool($value)) {
            $value = (int) $value;
            return "$value";
        }
        throw new BadParamTypeException();
    }

    /**
     * @param mixed $value
     * @return void
     * @throws BadParamTypeException
     */
    public function validate($value): void
    {
        if (!is_null($value) && !is_numeric($value) && !is_bool($value)) {
            throw new BadParamTypeException('Значение параметра должно быть числом, строкой с числом или null');
        }
    }
}
