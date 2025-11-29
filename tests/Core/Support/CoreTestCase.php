<?php

declare(strict_types=1);

namespace WeasyPrint\Tests\Core\Support;

use PHPUnit\Framework\TestCase;
use WeasyPrint\Service;

abstract class CoreTestCase extends TestCase
{
  use PdfAssertions;

  protected function createService(array $config = []): Service
  {
    return new Service($config);
  }
}
