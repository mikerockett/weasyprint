<?php

declare(strict_types=1);

use WeasyPrint\Contracts\WeasyPrint;
use WeasyPrint\Objects\Config;
use WeasyPrint\WeasyPrintFactory;

describe('direct instantiation', function (): void {
  test('can instantiate with no config', function (): void {
    $service = new WeasyPrintFactory();

    expect($service)->toBeInstanceOf(WeasyPrintFactory::class);
    expect($service)->toBeInstanceOf(WeasyPrint::class);
    expect($service->getConfig())->toBeInstanceOf(Config::class);
  });

  test('can instantiate with config array', function (): void {
    $service = new WeasyPrintFactory([
      'binary' => '/usr/bin/weasyprint',
      'timeout' => 120,
      'dpi' => 300,
    ]);

    expect($service)->toBeValidServiceInstance();
    expect($service->getConfig()->binary)->toBe('/usr/bin/weasyprint');
    expect($service->getConfig()->timeout)->toBe(120);
    expect($service->getConfig()->dpi)->toBe(300);
  });

  test('implements factory interface', function (): void {
    $service = new WeasyPrintFactory();

    expect($service)->toBeInstanceOf(WeasyPrint::class);
  });

  test('config is initialized on construction', function (): void {
    $service = new WeasyPrintFactory(['cachePrefix' => 'custom_prefix']);
    $config = $service->getConfig();

    expect($config)->toBeInstanceOf(Config::class);
    expect($config->cachePrefix)->toBe('custom_prefix');
    expect($config->timeout)->toBe(60); // default value
  });
});
