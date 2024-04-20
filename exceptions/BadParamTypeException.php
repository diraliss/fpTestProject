<?php

declare(strict_types=1);

namespace FpDbTest\exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

class BadParamTypeException extends Exception
{
    #[Pure] public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Параметр имеет неподходящих тип для вставки';
        }
        parent::__construct($message, $code, $previous);
    }
}
