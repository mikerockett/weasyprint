<?php

declare(strict_types=1);

use WeasyPrint\Contracts\Factory;
use WeasyPrint\Objects\Config;
use WeasyPrint\Service;

describe('direct instantiation', function (): void {
  test('can instantiate with no config', function (): void {
    $service = new Service();

    expect($service)->toBeInstanceOf(Service::class);
    expect($service)->toBeInstanceOf(Factory::class);
    expect($service->getConfig())->toBeInstanceOf(Config::class);
  });

  test('can instantiate with config array', function (): void {
    $service = new Service([
      'binary' => '/usr/bin/weasyprint',
      'timeout' => 120,
      'dpi' => 300,
    ]);

    expect($service)->toBeValidServiceInstance();
    expect($service->getConfig()->binary)->toBe('/usr/bin/weasyprint');
    expect($service->getConfig()->timeout)->toBe(120);
    expect($service->getConfig()->dpi)->toBe(300);
  });

  test('implements Factory interface', function (): void {
    $service = new Service();

    expect($service)->toBeInstanceOf(Factory::class);
  });

  test('config is initialized on construction', function (): void {
    $service = new Service(['cachePrefix' => 'custom_prefix']);
    $config = $service->getConfig();

    expect($config)->toBeInstanceOf(Config::class);
    expect($config->cachePrefix)->toBe('custom_prefix');
    expect($config->timeout)->toBe(60); // default value
  });
});
