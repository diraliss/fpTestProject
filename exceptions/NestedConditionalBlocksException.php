<?php

declare(strict_types=1);

namespace FpDbTest\exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

class NestedConditionalBlocksException extends Exception
{
    #[Pure] public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Обнаружена вложенная конструкция условных блоков';
        }
        parent::__construct($message, $code, $previous);
    }
}
