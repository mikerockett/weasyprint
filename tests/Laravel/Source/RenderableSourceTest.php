<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\Contracts\WeasyPrint;
use WeasyPrint\Exceptions\AttachmentNotFoundException;

describe('renderable sources', function (): void {
  test('can prepare from view', function (): void {
    $service = app(WeasyPrint::class);
    $service->prepareSource(view('test-pdf'));

    expect($service->sourceIsSet())->toBeTrue();
    expect($service->getSource()->get())->toBeInstanceOf(Renderable::class);
  });

  test('can render view to PDF', function (): void {
    $data = app(WeasyPrint::class)
      ->prepareSource(view('test-pdf'))
      ->getData();

    expect($data)->toBeValidPdfData();
  });

  test('throws exception when attachment not found', function (): void {
    app(WeasyPrint::class)
      ->prepareSource(view('test-pdf'))
      ->addAttachment('/path/to/non-existent-file')
      ->build();
  })->throws(AttachmentNotFoundException::class);
});
