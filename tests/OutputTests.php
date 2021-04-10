<?php

declare(strict_types=1);

namespace WeasyPrint\Tests;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use WeasyPrint\Enums\OutputType;
use WeasyPrint\Service;

class OutputTests extends TestCase
{
  /** @covers WeasyPrint\Service */
  public function testUsesPdfOutputTypeByDefault(): void
  {
    $service = Service::new();

    $this->assertInstanceOf(OutputType::class, $outputType = $service->getOutputType());
    $this->assertEquals(OutputType::pdf(), $outputType);
  }

  /** @covers WeasyPrint\Service */
  public function testCanSetPdfOutputType(): void
  {
    $service = Service::new()->to(OutputType::pdf());

    $this->assertInstanceOf(OutputType::class, $outputType = $service->getOutputType());
    $this->assertEquals(OutputType::pdf(), $outputType);
  }

  /** @covers WeasyPrint\Service */
  public function testCanSetPngOutputType(): void
  {
    $service = Service::new()->to(OutputType::png());

    $this->assertInstanceOf(OutputType::class, $outputType = $service->getOutputType());
    $this->assertEquals(OutputType::png(), $outputType);
  }

  /** @covers WeasyPrint\Service */
  public function testCanRenderPdfFromString()
  {
    $this->runPdfAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource('<p>WeasyPrint rocks!</p>')->toPdf()
      )
    );
  }

  /** @covers WeasyPrint\Service */
  public function testCanRenderPdfFromUrl()
  {
    $this->runPdfAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource('https://example.com')->toPdf()
      )
    );
  }

  /** @covers WeasyPrint\Service */
  public function testCanRenderPdfFromRenderable()
  {
    $this->runPdfAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource(view('test-pdf'))->toPdf()
      )
    );
  }

  /** @covers WeasyPrint\Service */
  public function testCanRenderPngFromString()
  {
    $this->runPngAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource('<p>WeasyPrint rocks!</p>')->toPng()
      )
    );
  }

  /** @covers WeasyPrint\Service */
  public function testCanRenderPngFromUrl()
  {
    $this->runPngAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource('https://example.com')->toPng()
      )
    );
  }

  /** @covers WeasyPrint\Service */
  public function testCanRenderPngFromRenderable()
  {
    $this->runPngAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource(view('test-png'))->toPng()
      )
    );
  }

  /** @covers WeasyPrint\Service */
  public function testCanRenderAndInlinePdfOutput()
  {
    $this->runOutputFileAssertions(
      Service::new()->prepareSource(view('test-pdf'))->toPdf()->build()->inline('test.pdf'),
      'application/pdf',
      'inline; filename=test.pdf'
    );
  }

  /** @covers WeasyPrint\Service */
  public function testCanRenderAndInlinePngOutput()
  {
    $this->runOutputFileAssertions(
      Service::new()->prepareSource(view('test-png'))->toPng()->build()->inline('test.png'),
      'image/png',
      'inline; filename=test.png'
    );
  }

  /** @covers WeasyPrint\Service */
  public function testCanRenderAndDownloadPdfOutput()
  {
    $this->runOutputFileAssertions(
      Service::new()->prepareSource(view('test-pdf'))->toPdf()->build()->download('test.pdf'),
      'application/pdf',
      'attachment; filename=test.pdf'
    );
  }

  /** @covers WeasyPrint\Service */
  public function testCanRenderAndDownloadPngOutput()
  {
    $this->runOutputFileAssertions(
      Service::new()->prepareSource(view('test-png'))->toPng()->build()->download('test.png'),
      'image/png',
      'attachment; filename=test.png'
    );
  }

  private function buildAndGetData(Service $service): string
  {
    return $service->build()->getData();
  }

  private function writeTempFile($contents)
  {
    $tempFilename = tempnam(
      sys_get_temp_dir(),
      config('weasyprint.cachePrefix', 'weasyprint_cache')
    );

    file_put_contents($tempFilename, $contents);

    return $tempFilename;
  }


  private function runPdfAssertions($output)
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

  private function runPngAssertions($output)
  {
    $tempFilename = $this->writeTempFile($output);

    $this->assertNotNull($output);
    $this->assertNotEmpty($output);
    $this->assertNotFalse(imagecreatefrompng($tempFilename));

    unlink($tempFilename);

    $this->assertFalse(is_file($tempFilename));
  }

  private function runOutputFileAssertions($output, string $expectedMime, string $expectedDisposition)
  {
    /** @var ResponseHeaderBag */
    $headers = $output->headers;

    $isResponse = $headers instanceof ResponseHeaderBag;

    $this->assertTrue($isResponse);

    if ($isResponse) {
      $this->assertTrue($headers->get('content-type') === $expectedMime);
      $this->assertTrue($headers->get('content-disposition') === $expectedDisposition);
    }
  }
}
