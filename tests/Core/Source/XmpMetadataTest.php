<?php

declare(strict_types=1);

use WeasyPrint\WeasyPrintFactory;
use WeasyPrint\Tests\Fixtures\SampleHtml;

describe('xmp metadata', function (): void {
  test('can add xmp metadata', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());
    $result = $service->addXmpMetadata('/path/to/rdf.xml');

    expect($result)->toBe($service);
    expect($service->getXmpMetadata())->toHaveCount(1);
    expect($service->getXmpMetadata()[0])->toBe('/path/to/rdf.xml');
  });

  test('can add multiple xmp metadata files', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $service
      ->addXmpMetadata('/path/to/first.xml')
      ->addXmpMetadata('/path/to/second.xml');

    expect($service->getXmpMetadata())->toHaveCount(2);
  });
});
