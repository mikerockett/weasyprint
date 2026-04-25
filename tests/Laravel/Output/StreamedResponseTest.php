<?php

declare(strict_types=1);

use WeasyPrint\Contracts\WeasyPrint;

describe('streamed responses', function (): void {
  it('can download via build then download', function (): void {
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

  it('can download via shorthand', function (): void {
    $response = app(WeasyPrint::class)
      ->prepareSource(view('test-pdf'))
      ->download('test.pdf');

    $this->runOutputAssertions(
      $response,
      'application/pdf',
      'attachment; filename=test.pdf',
    );
  });

  it('can inline via build then inline', function (): void {
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

  it('can inline via shorthand', function (): void {
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
