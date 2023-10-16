<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;

class OutputReadFailedException extends RuntimeException
{
  public function __construct(string $outputFilePath)
  {
    parent::__construct(
      sprintf(
        'The output file located at %s could not be streamed into memory.',
        $outputFilePath,
      )
    );
  }
}
