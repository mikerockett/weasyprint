<?php

declare(strict_types=1);

use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\{ResponseHeaderBag, StreamedResponse};
use WeasyPrint\Enums\PDFVersion;
use WeasyPrint\Service;

test('can render from string', function (): void {
  runPdfAssertions(buildAndGetData(
    Service::new()->prepareSource('<p>WeasyPrint rocks!</p>')
  ));
});

test('can render with create from source shorthand', function (): void {
  runPdfAssertions(buildAndGetData(
    Service::createFromSource('<p>WeasyPrint rocks!</p>')
  ));
});

test('can render from url', function (): void {
  runPdfAssertions(buildAndGetData(
    Service::new()->prepareSource('https://google.com')
  ));
});

test('can render from renderable', function (): void {
  runPdfAssertions(buildAndGetData(
    Service::new()->prepareSource(view('test-pdf'))
  ));
});

test('can render and inline pdf output', function (): void {
  runOutputFileAssertions(
    Service::new()
      ->prepareSource(view('test-pdf'))->build()
      ->inline('test.pdf'),
    'application/pdf',
    'inline; filename=test.pdf'
  );
});

test('can render and download pdf output', function (): void {
  runOutputFileAssertions(
    Service::new()
      ->prepareSource(view('test-pdf'))->build()
      ->download('test.pdf'),
    'application/pdf',
    'attachment; filename=test.pdf'
  );
});

test('can render and download pdf output with shorthands', function (): void {
  runOutputFileAssertions(
    Service::new()
      ->prepareSource(view('test-pdf'))
      ->download('test.pdf'),
    'application/pdf',
    'attachment; filename=test.pdf'
  );
});

test('can render different pdf versions', function (): void {
  $service = Service::new();

  collect([PDFVersion::VERSION_1_4, PDFVersion::VERSION_1_7])->each(
    function (PDFVersion $version): void {
      $data = Service::new()
        ->mergeConfig(pdfVersion: $version)
        ->prepareSource(view('test-pdf'))
        ->getData('test.pdf');

      expect($data)->toStartWith("%PDF-{$version->value}");
    }
  );
});

function buildAndGetData(Service $service): string
{
  return $service->build()->getData();
}

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
