<?php

declare(strict_types=1);

use WeasyPrint\Tests\Fixtures\TestPDF;

describe('abstract PDF class', function (): void {
  test('can download via class', function (): void {
    $response = (new TestPDF())->download();

    $this->runOutputAssertions(
      $response,
      'application/pdf',
      'attachment; filename=test.pdf',
    );
  });

  test('can inline via class', function (): void {
    $response = (new TestPDF())->inline();

    $this->runOutputAssertions(
      $response,
      'application/pdf',
      'inline; filename=test.pdf',
    );
  });

  test('can override default stream mode', function (): void {
    $response = (new TestPDF())->stream(\WeasyPrint\Enums\StreamMode::DOWNLOAD);

    $this->runOutputAssertions(
      $response,
      'application/pdf',
      'attachment; filename=test.pdf',
    );
  });

  test('respects custom headers', function (): void {
    $pdf = new class extends \WeasyPrint\PDF {
      public function source(): \Illuminate\Contracts\Support\Renderable
      {
        return view('test-pdf');
      }

      public function filename(): string
      {
        return 'custom.pdf';
      }

      public function headers(): array
      {
        return ['X-Custom' => 'Header'];
      }
    };

    $response = $pdf->inline();

    $this->runOutputAssertions(
      $response,
      'application/pdf',
      'inline; filename=custom.pdf',
      ['X-Custom' => 'Header'],
    );
  });
});
