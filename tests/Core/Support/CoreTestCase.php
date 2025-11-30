<?php

declare(strict_types=1);

namespace WeasyPrint\Tests\Core\Support;

use PHPUnit\Framework\TestCase;
use WeasyPrint\WeasyPrintFactory;

abstract class CoreTestCase extends TestCase
{
  use PdfAssertions;

  protected function createService(array $config = []): WeasyPrintFactory
  {
    return new WeasyPrintFactory($config);
  }
}
