<?php

declare(strict_types=1);

use WeasyPrint\Objects\Output;
use WeasyPrint\WeasyPrintFactory;
use WeasyPrint\Tests\Fixtures\SampleHtml;

describe('output stringable', function (): void {
  it('implements Stringable', function (): void {
    $output = new Output('test data');

    expect($output)->toBeInstanceOf(Stringable::class);
  });

  it('returns data when cast to string', function (): void {
    $output = new Output('test data');

    expect((string) $output)->toBe('test data');
  });

  it('can be cast to string from build output', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $output = $service->build();
    $string = (string) $output;

    expect($string)->toBe($output->data);
    expect($string)->not->toBeEmpty();
  });
});
