<?php

declare(strict_types=1);

use WeasyPrint\Contracts\WeasyPrint;
use WeasyPrint\Objects\Config;

describe('config loading', function (): void {
  test('config file can be loaded', function (): void {
    expect(config('weasyprint'))->toEqual(
      require __DIR__ . '/../../../src/Integration/Laravel/config/weasyprint.php',
    );
  });

  test('service receives config as Config object', function (): void {
    expect(
      app(WeasyPrint::class)->getConfig(),
    )->toBeInstanceOf(Config::class);
  });

  test('service config matches Laravel config', function (): void {
    expect(
      app(WeasyPrint::class)->getConfig()->toArray(),
    )->toEqual(config('weasyprint'));
  });
});
