<?php

declare(strict_types=1);

use WeasyPrint\Objects\Source;
use WeasyPrint\WeasyPrintFactory;
use WeasyPrint\Tests\Fixtures\SampleHtml;

describe('string sources', function (): void {
  it('can prepare from source instance', function (): void {
    $service = new WeasyPrintFactory();
    $source = new Source(SampleHtml::simple());
    $service->prepareSource($source);

    expect($service->sourceIsSet())->toBeTrue();
    expect($service->getSource())->toBeInstanceOf(Source::class);
    expect($service->getSource()->get())->toBeString();
  });

  it('can prepare from string argument', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    expect($service->sourceIsSet())->toBeTrue();
    expect($service->getSource())->toBeInstanceOf(Source::class);
    expect($service->getSource()->get())->toBe(SampleHtml::simple());
  });

  it('renders source to string content', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::withStyles());
    $rendered = $service->getSource()->render();

    expect($rendered)->toBeString();
    expect($rendered)->toContain('Styled PDF content');
  });

  it('returns self from prepareSource for fluent chaining', function (): void {
    $service = new WeasyPrintFactory();
    $result = $service->prepareSource('test content');

    expect($result)->toBe($service);
  });
});
