<?php

declare(strict_types=1);

use WeasyPrint\Integration\Laravel\WeasyPrint;

describe('facade', function (): void {
  it('can access service via facade', function (): void {
    expect(WeasyPrint::getFacadeRoot())->toBeValidServiceInstance();
  });

  it('proxies methods to the service via facade', function (): void {
    WeasyPrint::prepareSource('<p>Test</p>');

    expect(WeasyPrint::sourceIsSet())->toBeTrue();
  });
});
