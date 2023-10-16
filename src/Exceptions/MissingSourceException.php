<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;

class MissingSourceException extends RuntimeException
{
  public function __construct()
  {
    parent::__construct(
      'A source has not yet been set!'
    );
  }
}
