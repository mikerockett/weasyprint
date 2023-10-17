<?php

declare(strict_types=1);

use Illuminate\Support\Env;
use WeasyPrint\Enums\PDFVariant;
use WeasyPrint\Enums\PDFVersion;
use WeasyPrint\Objects\Config;
use WeasyPrint\Service;

describe('default config', function (): void {
  test('can be loaded', function (): void {
    expect(config('weasyprint'))->toEqual(
      require __DIR__ . '/../config/weasyprint.php'
    );
  });

  test('prepared as config object', function (): void {
    expect(
      Service::instance()->getConfig()
    )->toBeInstanceOf(Config::class);
  });

  test('prepared with defaults', function (): void {
    expect(
      Service::instance()->getConfig()->toArray()
    )->toEqual(config('weasyprint'));
  });
});

describe('runtime config', function (): void {
  test('via tapping', function (): void {
    expect(
      Service::instance()
        ->tapConfig(static function (Config $config) {
          $config->binary = '/bin/weasyprint';
        })
        ->getConfig()
        ->binary
    )->toEqual('/bin/weasyprint');
  });

  test('via overriding', function (): void {
    expect(
      Service::instance()
        ->setConfig(new Config(
          binary: '/bin/weasyprint'
        ))
        ->getConfig()
        ->binary
    )->toEqual('/bin/weasyprint');
  });
});

describe('environment', function (): void {
  test('sets pdf variant', function (): void {
    $this->scopeEnv(
      envKey: 'WEASYPRINT_PDF_VARIANT',
      envValue: 'pdf/a-1b',
      callback: fn (string $key) => expect(
        PDFVariant::fromEnvironment($key)
      )->toEqual(
        PDFVariant::PDF_A_1B
      )
    );
  });

  test('sets pdf version', function (): void {
    $this->scopeEnv(
      envKey: 'WEASYPRINT_PDF_VERSION',
      envValue: '1.7',
      callback: fn (string $key) => expect(
        PDFVersion::fromEnvironment($key)
      )->toEqual(
        PDFVersion::VERSION_1_7
      )
    );
  });
});
