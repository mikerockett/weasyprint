<?php

declare(strict_types=1);

use WeasyPrint\WeasyPrintFactory;

describe('version', function (): void {
  test('getWeasyPrintVersion returns a version string', function (): void {
    $service = new WeasyPrintFactory();
    $version = $service->getWeasyPrintVersion();

    expect($version)->toBeString();
    expect($version)->toMatch('/^\d+\.\d+/');
  });

  test('getWeasyPrintVersion does not include prefix', function (): void {
    $service = new WeasyPrintFactory();
    $version = $service->getWeasyPrintVersion();

    expect($version)->not->toContain('WeasyPrint version');
  });
});
