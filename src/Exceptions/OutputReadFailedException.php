<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;

class OutputReadFailedException extends RuntimeException implements WeasyPrintException
{
  public function __construct(string $outputFilePath)
  {
    parent::__construct(
      sprintf(
        'The output file at %s could not be read or was empty.',
        $outputFilePath,
      ),
    );
  }
}
