<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class LanguageNotSupportedException extends Exception
{
    public function __construct(string $message = "Language is not supported", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 