<?php

use WeasyPrint\Service;

uses(WeasyPrint\Tests\TestCase::class)->in(__DIR__);

expect()->extend('toBeValidServiceInstance', function () {
  return $this->toBeInstanceOf(Service::class);
});
