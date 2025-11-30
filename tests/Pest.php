<?php

declare(strict_types=1);

use WeasyPrint\WeasyPrintFactory;
use WeasyPrint\Tests\Core\Support\CoreTestCase;
use WeasyPrint\Tests\Laravel\Support\LaravelTestCase;

// Core tests use CoreTestCase (no Laravel dependencies)
uses(CoreTestCase::class)->in('Core');

// Laravel tests use LaravelTestCase (Orchestra Testbench)
uses(LaravelTestCase::class)->in('Laravel');

// Shared expectations
expect()->extend('toBeValidServiceInstance', function () {
  return $this->toBeInstanceOf(WeasyPrintFactory::class);
});

expect()->extend('toBeValidPdfData', function () {
  $this->toBeString();
  $this->not->toBeEmpty();
  return $this;
});
