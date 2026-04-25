<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;

class BinaryNotFoundException extends RuntimeException
{
  public function __construct(?string $binaryPath = null)
  {
    if ($binaryPath === null) {
      parent::__construct(
        'Unable to find WeasyPrint binary. Please specify the absolute path to WeasyPrint in config [binary].',
      );

      return;
    }

    parent::__construct(
      sprintf(
        'Configured WeasyPrint binary path is invalid or not executable: %s',
        $binaryPath,
      ),
    );
  }
}
