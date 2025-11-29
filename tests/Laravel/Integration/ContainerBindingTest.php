<?php

declare(strict_types=1);

use WeasyPrint\Contracts\Factory;

describe('container bindings', function (): void {
  test('can resolve via dependency injection', function (): void {
    expect($this->app->make(Factory::class))->toBeValidServiceInstance();
  });

  test('can resolve via app() helper', function (): void {
    expect(app(Factory::class))->toBeValidServiceInstance();
  });

  test('binding is scoped', function (): void {
    $first = app(Factory::class);
    $second = app(Factory::class);

    expect($first)->toBe($second);
  });
});
