<?php

declare(strict_types=1);

use WeasyPrint\Objects\Output;
use WeasyPrint\WeasyPrintFactory;
use WeasyPrint\Tests\Fixtures\SampleHtml;

describe('output stringable', function (): void {
  test('implements Stringable', function (): void {
    $output = new Output('test data');

    expect($output)->toBeInstanceOf(Stringable::class);
  });

  test('__toString returns data', function (): void {
    $output = new Output('test data');

    expect((string) $output)->toBe('test data');
  });

  test('can be cast to string from build output', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $output = $service->build();
    $string = (string) $output;

    expect($string)->toBe($output->data);
    expect($string)->not->toBeEmpty();
  });
});
