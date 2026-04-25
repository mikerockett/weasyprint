<?php

declare(strict_types=1);

use WeasyPrint\Exceptions\InvalidConfigValueException;
use WeasyPrint\Objects\Config;
use WeasyPrint\WeasyPrintFactory;

describe('config manipulation', function (): void {
  it('can get config', function (): void {
    $service = new WeasyPrintFactory(['timeout' => 90]);
    $config = $service->getConfig();

    expect($config)->toBeInstanceOf(Config::class);
    expect($config->timeout)->toBe(90);
  });

  it('can set config', function (): void {
    $service = new WeasyPrintFactory();
    $newConfig = new Config(binary: '/custom/path');
    $result = $service->setConfig($newConfig);

    expect($result)->toBe($service); // fluent interface
    expect($service->getConfig()->binary)->toBe('/custom/path');
  });

  it('can tap config', function (): void {
    $service = new WeasyPrintFactory();

    $result = $service->tapConfig(function (Config $config) {
      $config->binary = '/tapped/binary';
      $config->timeout = 45;
    });

    expect($result)->toBe($service); // fluent interface
    expect($service->getConfig()->binary)->toBe('/tapped/binary');
    expect($service->getConfig()->timeout)->toBe(45);
  });

  it('runs assertions when setting config', function (): void {
    $service = new WeasyPrintFactory();
    $service->setConfig(new Config(dpi: -10));
  })->throws(InvalidConfigValueException::class);

  it('runs assertions when tapping config', function (): void {
    $service = new WeasyPrintFactory();

    $service->tapConfig(function (Config $config) {
      $config->jpegQuality = 100;
    });
  })->throws(InvalidConfigValueException::class);
});
