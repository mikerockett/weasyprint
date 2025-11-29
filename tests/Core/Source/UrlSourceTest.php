<?php

declare(strict_types=1);

use WeasyPrint\Service;

describe('url sources', function (): void {
  test('can prepare from url', function (): void {
    $service = new Service();
    $url = 'https://example.org';
    $service->prepareSource($url);

    expect($service->sourceIsSet())->toBeTrue();
    expect($service->getSource()->isUrl())->toBeTrue();
  });

  test('isUrl() detects valid urls', function (): void {
    $service = new Service();

    $service->prepareSource('https://example.org');
    expect($service->getSource()->isUrl())->toBeTrue();

    $service->prepareSource('http://example.org');
    expect($service->getSource()->isUrl())->toBeTrue();

    $service->prepareSource('https://example.org/path');
    expect($service->getSource()->isUrl())->toBeTrue();
  });

  test('isUrl() returns false for non-urls', function (): void {
    $service = new Service();

    $service->prepareSource('<p>Not a URL</p>');
    expect($service->getSource()->isUrl())->toBeFalse();

    $service->prepareSource('/local/path');
    expect($service->getSource()->isUrl())->toBeFalse();
  });
});
