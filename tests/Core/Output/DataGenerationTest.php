<?php

declare(strict_types=1);

use WeasyPrint\Objects\Source;
use WeasyPrint\WeasyPrintService;
use WeasyPrint\Tests\Fixtures\SampleHtml;

describe('data generation', function (): void {
  test('can build and get data from string source', function (): void {
    $service = new WeasyPrintService();
    $service->prepareSource(SampleHtml::simple());

    $data = $service->build()->getData();

    expect($data)->toBeValidPdfData();
    $this->assertValidPdf($data);
  });

  test('can build and get data from Source instance', function (): void {
    $service = new WeasyPrintService();
    $service->prepareSource(new Source(SampleHtml::withStyles()));

    $data = $service->build()->getData();

    expect($data)->toBeValidPdfData();
    $this->assertValidPdf($data);
  });

  test('can build and get data from URL', function (): void {
    $service = new WeasyPrintService();
    $service->prepareSource('https://example.org');

    $data = $service->build()->getData();

    expect($data)->toBeValidPdfData();
    $this->assertValidPdf($data);
  });

  test('build() returns Output instance', function (): void {
    $service = new WeasyPrintService();
    $service->prepareSource(SampleHtml::simple());

    $output = $service->build();

    expect($output)->toBeInstanceOf(WeasyPrint\Objects\Output::class);
  });

  test('getData() shorthand works', function (): void {
    $service = new WeasyPrintService();
    $service->prepareSource(SampleHtml::simple());

    $data = $service->getData();

    expect($data)->toBeValidPdfData();
    $this->assertValidPdf($data);
  });
});
