<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;

class OutputReadFailedException extends RuntimeException
{
  public function __construct(string $outputFilePath)
  {
    parent::__construct("The output file $outputFilePath could not be streamed into memory.");
  }
}
