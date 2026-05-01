<?php

declare(strict_types=1);

namespace WeasyPrint\Enums;

enum MediaType: string
{
  use DerivesFromEnvironment;

  case PRINT = 'print';
  case SCREEN = 'screen';
  case ALL = 'all';
}
