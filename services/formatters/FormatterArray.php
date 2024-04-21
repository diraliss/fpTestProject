<?php

declare(strict_types=1);

namespace FpDbTest\services\formatters;

use FpDbTest\exceptions\BadFormatException;
use FpDbTest\exceptions\BadParamTypeException;
use FpDbTest\exceptions\UnexpectedSymbolInFieldNameException;
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
                if (str_contains($key, '`')) {
                    throw new UnexpectedSymbolInFieldNameException();
                }
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
            throw new BadParamTypeException('Значение параметра должно быть массивом');
        }
        $formatter = FormatterFactory::getFormatterByType();
        foreach ($value as $key => $item) {
            if (!is_string($key) && !is_int($key)) {
                throw new BadParamTypeException('Ключ массива должен быть или числом, или строкой');
            } elseif (is_string($key) && str_contains($key, '`')) {
                throw new UnexpectedSymbolInFieldNameException();
            }
            $formatter->validate($item);
        }
    }
}
