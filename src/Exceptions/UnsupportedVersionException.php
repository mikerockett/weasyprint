<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;
use WeasyPrint\Provider;

class UnsupportedVersionException extends RuntimeException
{
  public function __construct(string $installedVersion)
  {
    parent::__construct(
      sprintf(
        'You are running an unsupported version of WeasyPrint. You have version %s installed, which does not satisfy constraint %s.',
        $installedVersion,
        Provider::SUPPORTED_VERSIONS
      )
    );
  }
}
