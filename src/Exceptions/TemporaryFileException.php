<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;

class TemporaryFileException extends RuntimeException implements WeasyPrintException
{
  public function __construct(string $inputPath, ?string $reason = null)
  {
    parent::__construct(
      match ($reason) {
        null => sprintf('Unable to write a temporary file to %s', $inputPath),
        default => sprintf('Temporary file error for %s: %s', $inputPath, $reason),
      },
    );
  }
}
