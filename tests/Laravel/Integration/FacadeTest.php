<?php

declare(strict_types=1);

use WeasyPrint\Facade;

describe('facade', function (): void {
  test('can access service via facade', function (): void {
    expect(Facade::getFacadeRoot())->toBeValidServiceInstance();
  });

  test('facade proxies methods to service', function (): void {
    Facade::prepareSource('<p>Test</p>');

    expect(Facade::sourceIsSet())->toBeTrue();
  });
});
