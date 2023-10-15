<?php

declare(strict_types=1);

use Illuminate\Support\Env;
use WeasyPrint\Enums\{PDFVariant, PDFVersion};
use WeasyPrint\Objects\Config;
use WeasyPrint\Service;

test('defult configuration can be loaded', function (): void {
  expect(config('weasyprint'))->toEqual(
    require __DIR__ . '/../config/weasyprint.php'
  );
});

test('service prepares config object by default', function (): void {
  expect(Service::new()->getConfig())->toBeInstanceOf(Config::class);
});

test('service prepares default config with default options', function (): void {
  expect(Service::new()->getConfig()->toArray())->toEqual(config('weasyprint'));
});

test('service prepares default config with merged option', function (): void {
  expect(
    Service::new(
      binary: $binary = '/bin/weasyprint'
    )->getConfig()->getBinary()
  )->toEqual($binary);
});

test('service prepares default config with merged array option', function (): void {
  expect(
    Service::new(...[
      'binary' => $binary = '/bin/weasyprint',
    ])->getConfig()->getBinary()
  )->toEqual($binary);
});

test('config options can be changed after instantiation', function (): void {
  expect(
    Service::new()
      ->mergeConfig(binary: $binary = '/bin/weasyprint')
      ->getConfig()->getBinary()
  )->toEqual($binary);
});

test('pdf variant can be set from environment', function (): void {
  Env::getRepository()->set(
    $key = 'WEASYPRINT_PDF_VARIANT',
    'pdf/a-1b',
  );

  expect(PDFVariant::fromEnvironment($key))->toEqual(PDFVariant::PDF_A_1B);

  Env::getRepository()->clear($key);
});

test('pdf version can be set from environment', function (): void {
  Env::getRepository()->set(
    $key = 'WEASYPRINT_PDF_VERSION',
    '1.7',
  );

  expect(PDFVersion::fromEnvironment($key))->toEqual(PDFVersion::VERSION_1_7);

  Env::getRepository()->clear($key);
});
