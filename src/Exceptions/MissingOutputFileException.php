<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;

class MissingOutputFileException extends RuntimeException
{
  public function __construct(string $outputFilePath)
  {
    parent::__construct("An output file was expected at $outputFilePath, but one was not found.");
  }
}
