<?php

namespace WeasyPrint\Enums;

enum PDFVersion: string
{
  use DerivesFromEnvironment;

  case VERSION_1_4 = '1.4';
  case VERSION_1_7 = '1.7';
  case VERSION_2_0 = '2.0';
}
