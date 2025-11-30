<?php

declare(strict_types=1);

use WeasyPrint\Contracts\WeasyPrint;

describe('streamed responses', function (): void {
  test('download via build()->download()', function (): void {
    $response = app(WeasyPrint::class)
      ->prepareSource(view('test-pdf'))
      ->build()
      ->download('test.pdf');

    $this->runOutputAssertions(
      $response,
      'application/pdf',
      'attachment; filename=test.pdf',
    );
  });

  test('download via shorthand download()', function (): void {
    $response = app(WeasyPrint::class)
      ->prepareSource(view('test-pdf'))
      ->download('test.pdf');

    $this->runOutputAssertions(
      $response,
      'application/pdf',
      'attachment; filename=test.pdf',
    );
  });

  test('inline via build()->inline()', function (): void {
    $response = app(WeasyPrint::class)
      ->prepareSource(view('test-pdf'))
      ->build()
      ->inline('test.pdf');

    $this->runOutputAssertions(
      $response,
      'application/pdf',
      'inline; filename=test.pdf',
    );
  });

  test('inline via shorthand inline()', function (): void {
    $response = app(WeasyPrint::class)
      ->prepareSource(view('test-pdf'))
      ->inline('test.pdf');

    $this->runOutputAssertions(
      $response,
      'application/pdf',
      'inline; filename=test.pdf',
    );
  });
});
