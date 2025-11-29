<?php

declare(strict_types=1);

namespace WeasyPrint\Enums;

enum StreamMode
{
  case DOWNLOAD;
  case INLINE;

  public function disposition(): string
  {
    return match ($this) {
      static::DOWNLOAD => 'attachment',
      static::INLINE => 'inline',
    };
  }
}
