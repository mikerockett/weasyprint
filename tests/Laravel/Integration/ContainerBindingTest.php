<?php

declare(strict_types=1);

use WeasyPrint\Contracts\WeasyPrint;

describe('container bindings', function (): void {
  test('can resolve via dependency injection', function (): void {
    expect($this->app->make(WeasyPrint::class))->toBeValidServiceInstance();
  });

  test('can resolve via app() helper', function (): void {
    expect(app(WeasyPrint::class))->toBeValidServiceInstance();
  });

  test('binding is scoped', function (): void {
    $first = app(WeasyPrint::class);
    $second = app(WeasyPrint::class);

    expect($first)->toBe($second);
  });
});
