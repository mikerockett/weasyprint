<?php

declare(strict_types=1);

namespace WeasyPrint\Objects;

final readonly class Attachment
{
  public function __construct(
    public string $path,
    public ?string $relationship = null,
  ) {}
}
