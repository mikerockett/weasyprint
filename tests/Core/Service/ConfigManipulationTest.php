<?php

declare(strict_types=1);

use WeasyPrint\Exceptions\InvalidConfigValueException;
use WeasyPrint\Objects\Config;
use WeasyPrint\WeasyPrintService;

describe('config manipulation', function (): void {
  test('can get config', function (): void {
    $service = new WeasyPrintService(['timeout' => 90]);
    $config = $service->getConfig();

    expect($config)->toBeInstanceOf(Config::class);
    expect($config->timeout)->toBe(90);
  });

  test('can set config', function (): void {
    $service = new WeasyPrintService();
    $newConfig = new Config(binary: '/custom/path');
    $result = $service->setConfig($newConfig);

    expect($result)->toBe($service); // fluent interface
    expect($service->getConfig()->binary)->toBe('/custom/path');
  });

  test('can tap config', function (): void {
    $service = new WeasyPrintService();

    $result = $service->tapConfig(function (Config $config) {
      $config->binary = '/tapped/binary';
      $config->timeout = 45;
    });

    expect($result)->toBe($service); // fluent interface
    expect($service->getConfig()->binary)->toBe('/tapped/binary');
    expect($service->getConfig()->timeout)->toBe(45);
  });

  test('setConfig runs assertions', function (): void {
    $service = new WeasyPrintService();
    $service->setConfig(new Config(dpi: -10));
  })->throws(InvalidConfigValueException::class);

  test('tapConfig runs assertions', function (): void {
    $service = new WeasyPrintService();

    $service->tapConfig(function (Config $config) {
      $config->jpegQuality = 100;
    });
  })->throws(InvalidConfigValueException::class);
});
