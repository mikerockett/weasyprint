<?php

declare(strict_types=1);

use WeasyPrint\WeasyPrintFactory;

describe('version', function (): void {
  it('returns a version string', function (): void {
    $service = new WeasyPrintFactory();
    $version = $service->getWeasyPrintVersion();

    expect($version)->toBeString();
    expect($version)->toMatch('/^\d+\.\d+/');
  });

  it('does not include prefix in version string', function (): void {
    $service = new WeasyPrintFactory();
    $version = $service->getWeasyPrintVersion();

    expect($version)->not->toContain('WeasyPrint version');
  });
});
