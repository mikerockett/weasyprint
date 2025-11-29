<?php

declare(strict_types=1);

use WeasyPrint\Contracts\Factory;
use WeasyPrint\Facade;
use WeasyPrint\Service;

describe('can be instantiated via', function (): void {
  test('dependency injection', function (): void {
    expect($this->app->make(Factory::class))->toBeValidServiceInstance();
  });

  test('app() helper', function (): void {
    expect(app(Factory::class))->toBeValidServiceInstance();
  });

  test('facade', function (): void {
    expect(Facade::getFacadeRoot())->toBeValidServiceInstance();
  });
});
