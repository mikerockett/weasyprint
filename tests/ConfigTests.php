<?php

declare(strict_types=1);

namespace WeasyPrint\Tests;

use Illuminate\Config\Repository;
use WeasyPrint\{Objects\Config, Service};

/** @covers WeasyPrint\Service */
class ConfigTests extends TestCase
{
  public function testPackageLoadsDefaultConfig(): void
  {
    /** @var Repository */
    $containerConfig = $this->app->make(Repository::class);

    $this->assertEquals(
      require __DIR__ . '/../config/weasyprint.php',
      $containerConfig->get('weasyprint')
    );
  }

  public function testServicePreparesConfigObjectByDefault(): void
  {
    $config = Service::new()->getConfig();

    $this->assertInstanceOf(Config::class, $config);
  }

  public function testServicePreparesDefaultConfigWithDefaultOptions(): void
  {
    $config = Service::new()->getConfig();

    /** @var Repository */
    $containerConfig = $this->app->make(Repository::class);

    $this->assertEquals($containerConfig->get('weasyprint'), $config->toArray());
  }

  public function testServicePreparesDefaultConfigWithMergedOption(): void
  {
    $config = Service::new(binary: $binary = '/bin/weasyprint')->getConfig();

    $this->assertEquals($binary, $config->getBinary());
  }

  public function testServicePreparesDefaultConfigWithMergedArrayOption(): void
  {
    $config = Service::new(...[
      'binary' => $binary = '/bin/weasyprint'
    ])->getConfig();

    /** @var Repository */
    $containerConfig = $this->app->make(Repository::class);

    $this->assertEquals($binary, $config->getBinary());
    $this->assertEquals($containerConfig->get('weasyprint.cachePrefix'), $config->getCachePrefix());
  }

  public function testConfigOptionsCanBeChangedAfterInstantiation(): void
  {
    $config = Service::new()
      ->mergeConfig(binary: $binary = '/bin/weasyprint')
      ->getConfig();

    /** @var Repository */
    $containerConfig = $this->app->make(Repository::class);

    $this->assertEquals($binary, $config->getBinary());
    $this->assertEquals(
      $containerConfig->get('weasyprint.cachePrefix'),
      $config->getCachePrefix()
    );
  }
}
