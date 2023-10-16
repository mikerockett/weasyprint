<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;

class SourceNotSetException extends RuntimeException
{
  public function __construct()
  {
    parent::__construct(
      'A source has not been provided to the WeasyPrint service.'
    );
  }
}
