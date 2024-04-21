<?php

declare(strict_types=1);

namespace FpDbTest\services\formatters;

use FpDbTest\exceptions\BadParamTypeException;
use FpDbTest\exceptions\UnexpectedSymbolInFieldNameException;

class FormatterIdentity implements FormatterInterface
{
    /**
     * @param string|string[] $value
     * @return string
     * @throws BadParamTypeException
     */
    public function format($value): string
    {
        $this->validate($value);
        if (is_string($value)) {
            return "`$value`";
        } elseif (is_array($value)) {
            $value = array_map(function (string $item) {
                return "`$item`";
            }, $value);
            return implode(', ', $value);
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
        if (!is_array($value) && !is_string($value)) {
            throw new BadParamTypeException('Значение параметра должно быть строкой или массивом строк');
        }
        if (is_array($value)) {
            foreach ($value as $item) {
                if (!is_string($item)) {
                    throw new BadParamTypeException('Элемент массива должен быть строкой');
                } elseif (str_contains($item, '`')) {
                    throw new UnexpectedSymbolInFieldNameException();
                }
            }
        } else {
            if (str_contains($value, '`')) {
                throw new UnexpectedSymbolInFieldNameException();
            }
        }
    }
}
