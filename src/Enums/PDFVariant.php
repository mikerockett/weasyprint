<?php

declare(strict_types=1);

namespace WeasyPrint\Enums;

enum PDFVariant: string
{
  use DerivesFromEnvironment;

  // PDF/A (Archive) - Basic conformance variants
  case PDF_A_1B = 'pdf/a-1b';
  case PDF_A_2B = 'pdf/a-2b';
  case PDF_A_3B = 'pdf/a-3b';
  case PDF_A_4B = 'pdf/a-4b';

  // PDF/A - Accessibility conformance (requires tagged structure)
  case PDF_A_1A = 'pdf/a-1a';
  case PDF_A_2A = 'pdf/a-2a';
  case PDF_A_3A = 'pdf/a-3a';

  // PDF/A - Unicode variants
  case PDF_A_2U = 'pdf/a-2u';
  case PDF_A_3U = 'pdf/a-3u';
  case PDF_A_4U = 'pdf/a-4u';

  // PDF/A - Engineering and file attachment variants
  case PDF_A_4E = 'pdf/a-4e';
  case PDF_A_4F = 'pdf/a-4f';

  // PDF/UA - Universal accessibility
  case PDF_UA_1 = 'pdf/ua-1';
  case PDF_UA_2 = 'pdf/ua-2';

  // PDF/X - Exchange formats for printing and publishing
  case PDF_X_1A = 'pdf/x-1a';
  case PDF_X_3 = 'pdf/x-3';
  case PDF_X_4 = 'pdf/x-4';
  case PDF_X_5G = 'pdf/x-5g';

  // Debug variant
  case DEBUG = 'debug';
}
