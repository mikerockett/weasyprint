<?php

namespace WeasyPrint\Enums;

enum PDFVariant: string
{
  case PDF_A_1B = 'pdf/a-1b';
  case PDF_A_2B = 'pdf/a-2b';
  case PDF_A_3B = 'pdf/a-3b';
  case PDF_A_4B = 'pdf/a-4b';
  case PDF_UA_1 = 'pdf/ua-1';

  public static function fromEnv(string $key): ?static
  {
    return match ($env = env($key)) {
      null => null,
      default => self::tryFrom($env)
    };
  }
}
