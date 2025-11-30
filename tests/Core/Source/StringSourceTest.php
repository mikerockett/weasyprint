<?php

declare(strict_types=1);

use WeasyPrint\Objects\Source;
use WeasyPrint\WeasyPrintFactory;
use WeasyPrint\Tests\Fixtures\SampleHtml;

describe('string sources', function (): void {
  test('can prepare from source instance', function (): void {
    $service = new WeasyPrintFactory();
    $source = new Source(SampleHtml::simple());
    $service->prepareSource($source);

    expect($service->sourceIsSet())->toBeTrue();
    expect($service->getSource())->toBeInstanceOf(Source::class);
    expect($service->getSource()->get())->toBeString();
  });

  test('can prepare from string argument', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    expect($service->sourceIsSet())->toBeTrue();
    expect($service->getSource())->toBeInstanceOf(Source::class);
    expect($service->getSource()->get())->toBe(SampleHtml::simple());
  });

  test('source render() returns string content', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::withStyles());
    $rendered = $service->getSource()->render();

    expect($rendered)->toBeString();
    expect($rendered)->toContain('Styled PDF content');
  });

  test('prepareSource returns self for fluent interface', function (): void {
    $service = new WeasyPrintFactory();
    $result = $service->prepareSource('test content');

    expect($result)->toBe($service);
  });
});
