<?php

declare(strict_types=1);

use WeasyPrint\WeasyPrintService;

describe('url sources', function (): void {
  test('can prepare from url', function (): void {
    $service = new WeasyPrintService();
    $url = 'https://example.org';
    $service->prepareSource($url);

    expect($service->sourceIsSet())->toBeTrue();
    expect($service->getSource()->isUrl())->toBeTrue();
  });

  test('isUrl() detects valid urls', function (): void {
    $service = new WeasyPrintService();

    $service->prepareSource('https://example.org');
    expect($service->getSource()->isUrl())->toBeTrue();

    $service->prepareSource('http://example.org');
    expect($service->getSource()->isUrl())->toBeTrue();

    $service->prepareSource('https://example.org/path');
    expect($service->getSource()->isUrl())->toBeTrue();
  });

  test('isUrl() returns false for non-urls', function (): void {
    $service = new WeasyPrintService();

    $service->prepareSource('<p>Not a URL</p>');
    expect($service->getSource()->isUrl())->toBeFalse();

    $service->prepareSource('/local/path');
    expect($service->getSource()->isUrl())->toBeFalse();
  });
});
