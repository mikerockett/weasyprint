<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;

class TemporaryFileException extends RuntimeException
{
  public function __construct(string $inputPath)
  {
    parent::__construct("Unable to write a temporary file to $inputPath");
  }
}
