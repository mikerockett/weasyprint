<?php

declare(strict_types=1);

namespace WeasyPrint\Exceptions;

use RuntimeException;

class AttachmentNotFoundException extends RuntimeException
{
  public function __construct(string $attachmentPath)
  {
    parent::__construct("Unable to add attachment: file at $attachmentPath does not exist.");
  }
}
