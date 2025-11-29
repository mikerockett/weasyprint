<?php

declare(strict_types=1);

namespace WeasyPrint\Tests\Core\Support;

use PHPUnit\Framework\TestCase;
use WeasyPrint\WeasyPrintService;

abstract class CoreTestCase extends TestCase
{
  use PdfAssertions;

  protected function createService(array $config = []): WeasyPrintService
  {
    return new WeasyPrintService($config);
  }
}
