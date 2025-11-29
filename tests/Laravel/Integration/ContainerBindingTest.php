<?php

declare(strict_types=1);

use WeasyPrint\Contracts\WeasyPrintFactory;

describe('container bindings', function (): void {
  test('can resolve via dependency injection', function (): void {
    expect($this->app->make(WeasyPrintFactory::class))->toBeValidServiceInstance();
  });

  test('can resolve via app() helper', function (): void {
    expect(app(WeasyPrintFactory::class))->toBeValidServiceInstance();
  });

  test('binding is scoped', function (): void {
    $first = app(WeasyPrintFactory::class);
    $second = app(WeasyPrintFactory::class);

    expect($first)->toBe($second);
  });
});
