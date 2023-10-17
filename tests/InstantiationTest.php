<?php

declare(strict_types=1);

use WeasyPrint\Contracts\Factory;
use WeasyPrint\Facade;
use WeasyPrint\Service;

describe('can be instantiated via', function (): void {
  test('dependency injection', function (): void {
    expect($this->app->make(Factory::class))->toBeValidServiceInstance();
  });

  test('instance() helper', function (): void {
    expect(Service::instance())->toBeValidServiceInstance();
  });

  test('facade', function (): void {
    expect(Facade::getFacadeRoot())->toBeValidServiceInstance();
  });
});
