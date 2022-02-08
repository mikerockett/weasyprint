<?php

declare(strict_types=1);

namespace WeasyPrint\Tests;

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

  public function testCanRenderAndInlinePdfOutput()
  {
    $this->runOutputFileAssertions(
      Service::new()->prepareSource(view('test-pdf'))->build()->inline('test.pdf'),
      'application/pdf',
      'inline; filename=test.pdf'
    );
  }

  public function testCanRenderAndDownloadPdfOutput()
  {
    $this->runOutputFileAssertions(
      Service::new()->prepareSource(view('test-pdf'))->build()->download('test.pdf'),
      'application/pdf',
      'attachment; filename=test.pdf'
    );
  }

  public function testCanRenderAndDownloadPdfOutputWithShorthands()
  {
    $this->runOutputFileAssertions(
      Service::new()->prepareSource(view('test-pdf'))->download('test.pdf'),
      'application/pdf',
      'attachment; filename=test.pdf'
    );
  }

  protected function buildAndGetData(Service $service): string
  {
    return $service->build()->getData();
  }

  protected function writeTempFile($contents)
  {
    $tempFilename = tempnam(
      sys_get_temp_dir(),
      config('weasyprint.cachePrefix', 'weasyprint_cache')
    );

    file_put_contents($tempFilename, $contents);

    return $tempFilename;
  }


  protected function runPdfAssertions($output)
  {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tempFilename = $this->writeTempFile($output);
    $mime = finfo_file($finfo, $tempFilename);

    $this->assertNotNull($output);
    $this->assertNotEmpty($output);
    $this->assertEquals($mime, 'application/pdf');

    unlink($tempFilename);

    $this->assertFalse(is_file($tempFilename));
  }

  protected function runOutputFileAssertions($output, string $expectedMime, string $expectedDisposition)
  {
    /** @var ResponseHeaderBag */
    $headers = $output->headers;

    $hasHeaderBag = $headers instanceof ResponseHeaderBag;

    $this->assertTrue($output instanceof StreamedResponse);
    $this->assertTrue($hasHeaderBag);

    if ($hasHeaderBag) {
      $this->assertTrue($headers->get('content-type') === $expectedMime);
      $this->assertTrue($headers->get('content-disposition') === $expectedDisposition);
    }
  }
}
