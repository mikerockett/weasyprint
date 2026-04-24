<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\Enums\StreamMode;
use WeasyPrint\Integration\Laravel\PDF;
use WeasyPrint\Objects\Config;
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
    $response = (new TestPDF())->stream(StreamMode::DOWNLOAD);

    $this->runOutputAssertions(
      $response,
      'application/pdf',
      'attachment; filename=test.pdf',
    );
  });

  test('toResponse uses defaultStreamMode', function (): void {
    $response = (new TestPDF())->toResponse(request());

    $this->runOutputAssertions(
      $response,
      'application/pdf',
      'inline; filename=test.pdf',
    );
  });

  test('toResponse respects overridden defaultStreamMode', function (): void {
    $pdf = new class extends PDF {
      public function source(): Renderable
      {
        return view('test-pdf');
      }

      public function filename(): string
      {
        return 'test.pdf';
      }

      public function defaultStreamMode(): StreamMode
      {
        return StreamMode::DOWNLOAD;
      }
    };

    $response = $pdf->toResponse(request());

    $this->runOutputAssertions(
      $response,
      'application/pdf',
      'attachment; filename=test.pdf',
    );
  });

  test('config callback is invoked', function (): void {
    $configTapped = false;

    $pdf = new class($configTapped) extends PDF {
      public function __construct(private bool &$tapped) {}

      public function source(): Renderable
      {
        return view('test-pdf');
      }

      public function filename(): string
      {
        return 'test.pdf';
      }

      public function config(Config $config): void
      {
        $this->tapped = true;
      }
    };

    $pdf->inline();

    expect($configTapped)->toBeTrue();
  });

  test('respects custom headers', function (): void {
    $pdf = new class extends PDF {
      public function source(): Renderable
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
