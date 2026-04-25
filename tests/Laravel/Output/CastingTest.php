<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use WeasyPrint\Contracts\WeasyPrint;

describe('casting', function (): void {
  it('can cast to string for storage', function (): void {
    $output = app(WeasyPrint::class)
      ->prepareSource(view('test-pdf'))
      ->build();

    Storage::fake('weasyprint');
    $disk = Storage::disk('weasyprint');

    $disk->put('test.pdf', $output);
    $disk->assertExists('test.pdf');
  });
});
