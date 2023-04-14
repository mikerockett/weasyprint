<?php

declare(strict_types=1);

namespace WeasyPrint\Tests;

use Illuminate\Config\Repository;
use Illuminate\Support\Env;
use WeasyPrint\{Objects\Config, Service};
use WeasyPrint\Enums\PDFVariant;

/** @covers WeasyPrint\Service */
class ConfigTests extends TestCase
{
  public function testPackageLoadsDefaultConfig(): void
  {
    /** @var Repository */
    $containerConfig = $this->app->make(Repository::class);

    $this->assertEquals(
      expected: require __DIR__ . '/../config/weasyprint.php',
      actual: $containerConfig->get('weasyprint')
    );
  }

  public function testServicePreparesConfigObjectByDefault(): void
  {
    $config = Service::new()->getConfig();

    $this->assertInstanceOf(
      expected: Config::class,
      actual: $config
    );
  }

  public function testServicePreparesDefaultConfigWithDefaultOptions(): void
  {
    $config = Service::new()->getConfig();

    /** @var Repository */
    $containerConfig = $this->app->make(Repository::class);

    $this->assertEquals(
      expected: $containerConfig->get('weasyprint'),
      actual: $config->toArray()
    );
  }

  public function testServicePreparesDefaultConfigWithMergedOption(): void
  {
    $config = Service::new(binary: $binary = '/bin/weasyprint')->getConfig();

    $this->assertEquals(
      expected: $binary,
      actual: $config->getBinary(),
    );
  }

  public function testServicePreparesDefaultConfigWithMergedArrayOption(): void
  {
    $config = Service::new(...[
      'binary' => $binary = '/bin/weasyprint'
    ])->getConfig();

    /** @var Repository */
    $containerConfig = $this->app->make(Repository::class);

    $this->assertEquals(
      expected: $binary,
      actual: $config->getBinary(),
    );

    $this->assertEquals(
      expected: $containerConfig->get('weasyprint.cachePrefix'),
      actual: $config->getCachePrefix(),
    );
  }

  public function testConfigOptionsCanBeChangedAfterInstantiation(): void
  {
    $config = Service::new()
      ->mergeConfig(binary: $binary = '/bin/weasyprint')
      ->getConfig();

    /** @var Repository */
    $containerConfig = $this->app->make(Repository::class);

    $this->assertEquals(
      expected: $binary,
      actual: $config->getBinary(),
    );

    $this->assertEquals(
      expected: $containerConfig->get('weasyprint.cachePrefix'),
      actual: $config->getCachePrefix(),
    );
  }

  public function testPdfVariantCanBeSetFromEnv(): void
  {
    Env::getRepository()->set(
      name: $key = 'WEASYPRINT_PDF_VARIANT',
      value: 'pdf/a-1b',
    );

    $this->assertEquals(
      expected: PDFVariant::PDF_A_1B,
      actual: PDFVariant::fromEnv($key),
    );
  }
}
