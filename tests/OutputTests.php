<?php

declare(strict_types=1);

namespace WeasyPrint\Tests;

use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\{ResponseHeaderBag, StreamedResponse};
use WeasyPrint\Service;

/** @covers WeasyPrint\Service */
class OutputTests extends TestCase
{
  public function testCanRenderFromString(): void
  {
    $this->runPdfAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource('<p>WeasyPrint rocks!</p>')
      )
    );
  }

  public function testCanRenderWithCreateFromSourceShorthand(): void
  {
    $this->runPdfAssertions(
      $this->buildAndGetData(
        Service::createFromSource('<p>WeasyPrint rocks!</p>')
      )
    );
  }

  public function testCanRenderFromUrl(): void
  {
    $this->runPdfAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource('https://google.com')
      )
    );
  }

  public function testCanRenderFromRenderable(): void
  {
    $this->runPdfAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource(view('test-pdf'))
      )
    );
  }

  public function testCanRenderAndInlinePdfOutput(): void
  {
    $this->runOutputFileAssertions(
      Service::new()
        ->prepareSource(view('test-pdf'))->build()
        ->inline('test.pdf'),
      'application/pdf',
      'inline; filename=test.pdf'
    );
  }

  public function testCanRenderAndDownloadPdfOutput(): void
  {
    $this->runOutputFileAssertions(
      Service::new()
        ->prepareSource(view('test-pdf'))->build()
        ->download('test.pdf'),
      'application/pdf',
      'attachment; filename=test.pdf'
    );
  }

  public function testCanRenderAndDownloadPdfOutputWithShorthands(): void
  {
    $this->runOutputFileAssertions(
      Service::new()
        ->prepareSource(view('test-pdf'))
        ->download('test.pdf'),
      'application/pdf',
      'attachment; filename=test.pdf'
    );
  }

  public function testCanRenderDifferentPdfVersions(): void
  {
    $service = Service::new();

    if (version_compare($service->getWeasyPrintVersion(), '58', 'lt')) {
      $this->markTestSkipped('This test was skipped as it is not applicable to the current version of WeasyPrint.');
    }

    collect(['1.4', '1.7'])->each(function (string $version) {
      $data = Service::new()
        ->mergeConfig(pdfVersion: $version)
        ->prepareSource(view('test-pdf'))
        ->getData('test.pdf');

      $this->assertStringStartsWith(
        prefix: "%PDF-$version",
        string: $data
      );
    });
  }

  protected function buildAndGetData(Service $service): string
  {
    return $service->build()->getData();
  }

  protected function writeTempFile($contents): string
  {
    $tempFilename = tempnam(
      sys_get_temp_dir(),
      config('weasyprint.cachePrefix', 'weasyprint_cache')
    );

    file_put_contents($tempFilename, $contents);

    return $tempFilename;
  }

  protected function runPdfAssertions($output): void
  {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tempFilename = $this->writeTempFile($output);
    $mime = finfo_file($finfo, $tempFilename);

    $this->assertNotNull($output);
    $this->assertNotEmpty($output);

    $this->assertEquals(
      expected: 'application/pdf',
      actual: $mime,
    );

    $parser = new Parser();
    $document = $parser->parseFile($tempFilename);

    $this->assertStringStartsWith(
      prefix: 'WeasyPrint',
      string: $document->getDetails()['Producer']
    );

    unlink($tempFilename);

    $this->assertFalse(is_file($tempFilename));
  }

  protected function runOutputFileAssertions(
    mixed $output,
    string $expectedMime,
    string $expectedDisposition
  ): void {
    $headers = $output->headers;
    $hasHeaderBag = $headers instanceof ResponseHeaderBag;

    $this->assertTrue($output instanceof StreamedResponse);
    $this->assertTrue($hasHeaderBag);

    if ($hasHeaderBag) {
      $this->assertEquals(
        expected: $expectedMime,
        actual: $headers->get('content-type'),
      );

      $this->assertEquals(
        expected: $expectedDisposition,
        actual: $headers->get('content-disposition'),
      );
    }
  }
}
