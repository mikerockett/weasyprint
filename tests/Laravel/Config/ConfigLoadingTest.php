<?php

declare(strict_types=1);

use WeasyPrint\Contracts\WeasyPrint;
use WeasyPrint\Objects\Config;

describe('config loading', function (): void {
  it('can load config file', function (): void {
    expect(config('weasyprint'))->toEqual(
      require __DIR__ . '/../../../src/Integration/Laravel/config/weasyprint.php',
    );
  });

  it('receives config as a Config object', function (): void {
    expect(
      app(WeasyPrint::class)->getConfig(),
    )->toBeInstanceOf(Config::class);
  });

  it('matches service config to Laravel config', function (): void {
    expect(
      app(WeasyPrint::class)->getConfig()->toArray(),
    )->toEqual(config('weasyprint'));
  });
});
