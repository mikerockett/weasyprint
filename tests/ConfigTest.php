<?php

declare(strict_types=1);

use WeasyPrint\Enums\PDFVariant;
use WeasyPrint\Enums\PDFVersion;
use WeasyPrint\Exceptions\InvalidConfigValueException;
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
      callback: fn(string $key) => expect(
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
      callback: fn(string $key) => expect(
        PDFVersion::fromEnvironment($key)
      )->toEqual(
        PDFVersion::VERSION_1_7
      )
    );
  });
});

describe('validation', function (): void {
  test('valid dpi', function (): void {
    Service::instance()->tapConfig(function (Config $config) {
      $config->dpi = 300;
    });
  })->throwsNoExceptions();

  test('invalid dpi', function (): void {
    Service::instance()->tapConfig(function (Config $config) {
      $config->dpi = -10;
    });
  })->throws(InvalidConfigValueException::class);

  test('valid jpeg quality', function (): void {
    Service::instance()->tapConfig(function (Config $config) {
      $config->jpegQuality = 50;
    });
  })->throwsNoExceptions();

  test('invalid jpeg quality', function (): void {
    Service::instance()->tapConfig(function (Config $config) {
      $config->jpegQuality = 99;
    });
  })->throws(InvalidConfigValueException::class);

  test('valid input encoding', function (): void {
    Service::instance()->tapConfig(function (Config $config) {
      $config->inputEncoding = 'utf-16';
    });
  })->throwsNoExceptions();

  test('invalid input encoding', function (): void {
    Service::instance()->tapConfig(function (Config $config) {
      $config->inputEncoding = 'non-existent';
    });
  })->throws(InvalidConfigValueException::class);
});
