<?php

declare(strict_types=1);

namespace FpDbTest\exceptions;

use Exception;
use FpDbTest\dictionaries\FormatDictionary;
use JetBrains\PhpStorm\Pure;
use Throwable;

class BadFormatException extends Exception
{
    #[Pure] public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Некорректная метка вставки. '
                . 'Метки ограничены значениями: ' . implode(', ', FormatDictionary::getTypes());
        }
        parent::__construct($message, $code, $previous);
    }
}
