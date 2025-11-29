<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use WeasyPrint\Contracts\WeasyPrintFactory;

describe('casting', function (): void {
  test('to string for storage', function (): void {
    $output = app(WeasyPrintFactory::class)
      ->prepareSource(view('test-pdf'))
      ->build();

    Storage::fake('weasyprint');
    $disk = Storage::disk('weasyprint');

    $disk->put('test.pdf', $output);
    $disk->assertExists('test.pdf');
  });
});
