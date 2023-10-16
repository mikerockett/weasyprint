<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;

class MissingOutputFileException extends RuntimeException
{
  public function __construct(string $outputFilePath)
  {
    parent::__construct(
      sprintf(
        'An output file was expected at %s, but one was not found.',
        $outputFilePath
      )
    );
  }
}
