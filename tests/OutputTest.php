<?php

declare(strict_types=1);

use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\{ResponseHeaderBag, StreamedResponse};
use WeasyPrint\Enums\PDFVersion;
use WeasyPrint\Objects\Config;
use WeasyPrint\Service;

test('can render from string', function (): void {
  runPdfAssertions(
    Service::instance()->prepareSource('<p>WeasyPrint rocks!</p>')
      ->build()
      ->getData()
  );
});

test('can render from url', function (): void {
  runPdfAssertions(
    Service::instance()->prepareSource('https://google.com')
      ->build()
      ->getData()
  );
});

test('can render from renderable', function (): void {
  runPdfAssertions(
    Service::instance()->prepareSource(view('test-pdf'))
      ->build()
      ->getData()
  );
});

test('can render and inline pdf output', function (): void {
  runOutputFileAssertions(
    Service::instance()
      ->prepareSource(view('test-pdf'))->build()
      ->inline('test.pdf'),
    'application/pdf',
    'inline; filename=test.pdf'
  );
});

test('can render and download pdf output', function (): void {
  runOutputFileAssertions(
    Service::instance()
      ->prepareSource(view('test-pdf'))->build()
      ->download('test.pdf'),
    'application/pdf',
    'attachment; filename=test.pdf'
  );
});

test('can render and download pdf output with shorthands', function (): void {
  runOutputFileAssertions(
    Service::instance()
      ->prepareSource(view('test-pdf'))
      ->download('test.pdf'),
    'application/pdf',
    'attachment; filename=test.pdf'
  );
});

test('can render different pdf versions', function (): void {
  collect([PDFVersion::VERSION_1_4, PDFVersion::VERSION_1_7])->each(
    function (PDFVersion $version): void {
      $data = Service::instance()
        ->tapConfig(static function (Config $config) use ($version) {
          $config->pdfVersion = $version;
        })
        ->prepareSource(view('test-pdf'))
        ->getData('test.pdf');

      expect($data)->toStartWith("%PDF-{$version->value}");
    }
  );
});

function writeTempFile($contents): string
{
  file_put_contents(
    filename: $tempFilename = tempnam(
      sys_get_temp_dir(),
      config('weasyprint.cachePrefix', 'weasyprint_cache')
    ),
    data: $contents
  );

  return $tempFilename;
}

function runPdfAssertions($output): void
{
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $tempFilename = writeTempFile($output);
  $mime = finfo_file($finfo, $tempFilename);

  expect($output)->not->toBeNull();
  expect($output)->not->toBeEmpty();
  expect($mime)->toEqual('application/pdf');

  $parser = new Parser();
  $document = $parser->parseFile($tempFilename);

  expect($document->getDetails()['Producer'])->toStartWith('WeasyPrint');
  unlink($tempFilename);
  expect(is_file($tempFilename))->toBeFalse();
}

function runOutputFileAssertions(mixed $output, string $expectedMime, string $expectedDisposition): void
{
  $headers = $output->headers;
  $hasHeaderBag = $headers instanceof ResponseHeaderBag;

  expect($output instanceof StreamedResponse)->toBeTrue();
  expect($hasHeaderBag)->toBeTrue();

  if ($hasHeaderBag) {
    expect($headers->get('content-type'))->toEqual($expectedMime);
    expect($headers->get('content-disposition'))->toEqual($expectedDisposition);
  }
}
