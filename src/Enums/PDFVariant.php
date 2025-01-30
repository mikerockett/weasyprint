<?php

namespace WeasyPrint\Enums;

enum PDFVariant: string
{
  use DerivesFromEnvironment;

  case PDF_A_1B = 'pdf/a-1b';
  case PDF_A_2B = 'pdf/a-2b';
  case PDF_A_3B = 'pdf/a-3b';
  case PDF_A_4B = 'pdf/a-4b';

  case PDF_A_2U = 'pdf/a-2u';
  case PDF_A_3U = 'pdf/a-3u';
  case PDF_A_4U = 'pdf/a-4u';

  case PDF_UA_1 = 'pdf/ua-1';
}
