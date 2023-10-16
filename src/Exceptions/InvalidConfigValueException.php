<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;

class InvalidConfigValueException extends RuntimeException
{
  public function __construct(string $key, string $value, string $expected)
  {
    parent::__construct(
      sprintf(
        'Config value for %s is invalid. Provided %s, but expected %s.',
        $key,
        $value,
        $expected
      )
    );
  }
}
