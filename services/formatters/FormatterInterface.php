<?php

declare(strict_types=1);

namespace FpDbTest\services\formatters;

use FpDbTest\exceptions\BadParamTypeException;

interface FormatterInterface
{
    /**
     * @param $value
     * @return string
     * @throws BadParamTypeException
     */
    public function format($value): string;

    /**
     * @param $value
     * @return void
     * @throws BadParamTypeException
     */
    public function validate($value): void;
}
