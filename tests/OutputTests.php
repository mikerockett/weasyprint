<?php

declare(strict_types=1);

namespace WeasyPrint\Tests;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use UnexpectedValueException;
use WeasyPrint\Enums\OutputType;
use WeasyPrint\Service;

/** @covers WeasyPrint\Service */
class OutputTests extends TestCase
{
  public function testUsesPdfOutputTypeByDefault(): void
  {
    $service = Service::new();

    $this->assertInstanceOf(OutputType::class, $outputType = $service->getOutputType());
    $this->assertEquals(OutputType::pdf(), $outputType);
  }

  public function testCanSetPdfOutputType(): void
  {
    $service = Service::new()->to(OutputType::pdf());

    $this->assertInstanceOf(OutputType::class, $outputType = $service->getOutputType());
    $this->assertEquals(OutputType::pdf(), $outputType);
  }

  public function testCanSetPngOutputType(): void
  {
    $service = Service::new()->to(OutputType::png());

    $this->assertInstanceOf(OutputType::class, $outputType = $service->getOutputType());
    $this->assertEquals(OutputType::png(), $outputType);
  }

  public function testCanSetOutputTypeFromString(): void
  {
    $service = Service::new()->to(OutputType::from('pdf'));

    $this->assertInstanceOf(OutputType::class, $outputType = $service->getOutputType());
    $this->assertEquals(OutputType::pdf(), $outputType);
  }

  public function testFailsWhenInvalidOutputTypePassed(): void
  {
    $this->expectException(UnexpectedValueException::class);
    Service::new()->to(OutputType::from('jpg'));
  }

  public function testCanRenderPdfDefaultFromString()
  {
    $this->runPdfAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource('<p>WeasyPrint rocks!</p>')
      )
    );
  }

  public function testCanRenderPdfShorthandFromString()
  {
    $this->runPdfAssertions(
      $this->buildAndGetData(
        Service::createFromSource('<p>WeasyPrint rocks!</p>')
      )
    );
  }

  public function testCanRenderPdfFromString()
  {
    $this->runPdfAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource('<p>WeasyPrint rocks!</p>')->toPdf()
      )
    );
  }

  public function testCanRenderPdfFromUrl()
  {
    $this->runPdfAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource('https://example.com')->toPdf()
      )
    );
  }

  public function testCanRenderPdfFromRenderable()
  {
    $this->runPdfAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource(view('test-pdf'))->toPdf()
      )
    );
  }

  public function testCanRenderPngFromString()
  {
    $this->runPngAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource('<p>WeasyPrint rocks!</p>')->toPng()
      )
    );
  }

  public function testCanRenderPngFromUrl()
  {
    $this->runPngAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource('https://example.com')->toPng()
      )
    );
  }

  public function testCanRenderPngFromRenderable()
  {
    $this->runPngAssertions(
      $this->buildAndGetData(
        Service::new()->prepareSource(view('test-png'))->toPng()
      )
    );
  }

  public function testCanRenderAndInlinePdfOutput()
  {
    $this->runOutputFileAssertions(
      Service::new()->prepareSource(view('test-pdf'))->toPdf()->build()->inline('test.pdf'),
      'application/pdf',
      'inline; filename=test.pdf'
    );
  }

  public function testCanRenderAndInlinePngOutput()
  {
    $this->runOutputFileAssertions(
      Service::new()->prepareSource(view('test-png'))->toPng()->build()->inline('test.png'),
      'image/png',
      'inline; filename=test.png'
    );
  }

  public function testCanRenderAndDownloadPdfOutput()
  {
    $this->runOutputFileAssertions(
      Service::new()->prepareSource(view('test-pdf'))->toPdf()->build()->download('test.pdf'),
      'application/pdf',
      'attachment; filename=test.pdf'
    );
  }

  public function testCanRenderAndDownloadPngOutput()
  {
    $this->runOutputFileAssertions(
      Service::new()->prepareSource(view('test-png'))->toPng()->build()->download('test.png'),
      'image/png',
      'attachment; filename=test.png'
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
