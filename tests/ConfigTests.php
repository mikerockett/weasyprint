<?php

declare(strict_types=1);

namespace WeasyPrint\Tests;

use Illuminate\Config\Repository;
use WeasyPrint\Objects\Config;
use WeasyPrint\Service;

class ConfigTests extends TestCase
{
  /** @covers WeasyPrint\Service */
  public function testPackageLoadsDefaultConfig(): void
  {
    /** @var Repository */
    $containerConfig = $this->app->make(Repository::class);

    $this->assertEquals(
      require __DIR__ . '/../config/weasyprint.php',
      $containerConfig->get('weasyprint')
    );
  }

  /** @covers WeasyPrint\Service */
  public function testServicePreparesConfigObjectByDefault(): void
  {
    $config = Service::new()->getConfig();

    $this->assertInstanceOf(Config::class, $config);
  }

  /** @covers WeasyPrint\Service */
  public function testServicePreparesDefaultConfigWithDefaultOptions(): void
  {
    $config = Service::new()->getConfig();

    /** @var Repository */
    $containerConfig = $this->app->make(Repository::class);

    $this->assertEquals($containerConfig->get('weasyprint'), $config->toArray());
  }

  /** @covers WeasyPrint\Service */
  public function testServicePreparesDefaultConfigWithMergedOption(): void
  {
    $config = Service::new(binary: $binary = '/bin/weasyprint')->getConfig();

    $this->assertEquals($binary, $config->getBinary());
  }

  /** @covers WeasyPrint\Service */
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

  /** @covers WeasyPrint\Service */
  public function testConfigOptionsCanBeChangedAfterInstantiation(): void
  {
    $config = Service::new()
      ->withConfiguration(binary: $binary = '/bin/weasyprint')
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
