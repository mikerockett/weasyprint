<?php

declare(strict_types=1);

use WeasyPrint\Enums\PDFVersion;
use WeasyPrint\Objects\Config;
use WeasyPrint\Objects\Source;
use WeasyPrint\Service;
use WeasyPrint\Tests\Fixtures\TestPDF;

describe('from source', function (): void {
  test('can render', function (mixed $source): void {
    $this->runPdfAssertions(
      Service::instance()
        ->prepareSource($source)
        ->build()
        ->getData()
    );
  })->with([
    'instance' => fn() => new Source('<p>WeasyPrint rocks!</p>'),
    'argument' => fn() => '<p>WeasyPrint rocks!</p>',
    'url' => fn() => 'https://example.org',
    'renderable' => fn() => view('test-pdf'),
  ]);
});

describe('streamed responses', function (): void {
  test('download via output object', function (): void {
    $this->runOutputAssertions(
      Service::instance()
        ->prepareSource(view('test-pdf'))
        ->build()
        ->download('test.pdf'),
      'application/pdf',
      'attachment; filename=test.pdf'
    );
  });

  test('download via shorthand', function (): void {
    $this->runOutputAssertions(
      Service::instance()
        ->prepareSource(view('test-pdf'))
        ->download('test.pdf'),
      'application/pdf',
      'attachment; filename=test.pdf'
    );
  });

  test('download via class', function (): void {
    $this->runOutputAssertions(
      (new TestPDF())->download(),
      'application/pdf',
      'attachment; filename=test.pdf'
    );
  });

  test('inline via output object', function (): void {
    $this->runOutputAssertions(
      Service::instance()
        ->prepareSource(view('test-pdf'))
        ->build()
        ->inline('test.pdf'),
      'application/pdf',
      'inline; filename=test.pdf'
    );
  });

  test('inline via shorthand', function (): void {
    $this->runOutputAssertions(
      Service::instance()
        ->prepareSource(view('test-pdf'))
        ->inline('test.pdf'),
      'application/pdf',
      'inline; filename=test.pdf'
    );
  });

  test('inline via class', function (): void {
    $this->runOutputAssertions(
      (new TestPDF())->inline(),
      'application/pdf',
      'inline; filename=test.pdf'
    );
  });
});


describe('versions', function (): void {
  test('can render', function (PDFVersion $version): void {
    $data = Service::instance()
      ->tapConfig(static function (Config $config) use ($version): void {
        $config->pdfVersion = $version;
      })
      ->prepareSource(view('test-pdf'))
      ->getData();

    expect($data)->toStartWith("%PDF-{$version->value}");
  })->with([
    'version 1.4' => PDFVersion::VERSION_1_4,
    'version 1.7' => PDFVersion::VERSION_1_4,
    'version 2.0' => PDFVersion::VERSION_1_4,
  ]);
});
