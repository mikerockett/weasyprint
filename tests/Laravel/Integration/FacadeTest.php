<?php

declare(strict_types=1);

use WeasyPrint\Integration\Laravel\WeasyPrint;

describe('facade', function (): void {
  test('can access service via facade', function (): void {
    expect(WeasyPrint::getFacadeRoot())->toBeValidServiceInstance();
  });

  test('facade proxies methods to service', function (): void {
    WeasyPrint::prepareSource('<p>Test</p>');

    expect(WeasyPrint::sourceIsSet())->toBeTrue();
  });
});
