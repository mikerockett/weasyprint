<?php

declare(strict_types=1);

use WeasyPrint\Objects\Output;
use WeasyPrint\Objects\Source;
use WeasyPrint\WeasyPrintFactory;
use WeasyPrint\Tests\Fixtures\SampleHtml;

describe('data generation', function (): void {
  it('can build and get data from string source', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $data = $service->build()->data;

    expect($data)->toBeValidPdfData();
    $this->assertValidPdf($data);
  });

  it('can build and get data from Source instance', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(new Source(SampleHtml::withStyles()));

    $data = $service->build()->data;

    expect($data)->toBeValidPdfData();
    $this->assertValidPdf($data);
  });

  it('can build and get data from URL', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource('https://example.org');

    $data = $service->build()->data;

    expect($data)->toBeValidPdfData();
    $this->assertValidPdf($data);
  });

  it('returns an Output instance from build', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $output = $service->build();

    expect($output)->toBeInstanceOf(Output::class);
  });

  it('keeps output data immutable', function (): void {
    $output = new Output('test');
    $output->data = 'changed';
  })->throws(Error::class);

  it('can get data via shorthand', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $data = $service->getData();

    expect($data)->toBeValidPdfData();
    $this->assertValidPdf($data);
  });
});
