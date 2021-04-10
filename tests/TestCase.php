<?php

declare(strict_types=1);

namespace WeasyPrint\Tests;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use WeasyPrint\Provider;

abstract class TestCase extends OrchestraTestCase
{
  protected function getPackageProviders($app)
  {
    return [Provider::class];
  }

  /** @param Application $app */
  protected function getEnvironmentSetUp($app)
  {
    $app->make(Repository::class)->set('view.paths', [__DIR__ . '/views']);
  }
}
