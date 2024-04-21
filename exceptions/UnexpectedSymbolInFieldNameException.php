<?php

declare(strict_types=1);

namespace FpDbTest\exceptions;

use JetBrains\PhpStorm\Pure;
use Throwable;

class UnexpectedSymbolInFieldNameException extends BadParamTypeException
{
    #[Pure] public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'В названии поля не может использоваться символ "`"';
        }
        parent::__construct($message, $code, $previous);
    }
}
