<?php

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use WeasyPrint\WeasyPrint;
use WeasyPrint\WeasyPrintProvider;

class WeasyPrintTest extends Orchestra\Testbench\TestCase
{
  protected function getPackageProviders($app)
  {
    return [WeasyPrintProvider::class];
  }

  protected function getEnvironmentSetUp($app)
  {
    $app['config']->set('view.paths', [__DIR__ . '/views']);
  }

  private function writeTempFile($contents)
  {
    $tempFilename = tempnam(
      sys_get_temp_dir(),
      config('weasyprint.cache_prefix', 'weasyprint-cache_')
    );

    file_put_contents($tempFilename, $contents);

    return $tempFilename;
  }

  public function testCanReturnVersion()
  {
    $version = WeasyPrint::version();

    $this->assertNotNull($version);
    $this->assertNotEmpty($version);
    $this->assertStringStartsWith('WeasyPrint', $version);
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

  public function testCanRenderPdfFromUrl()
  {
    $this->runPdfAssertions(
      WeasyPrint::make('https://weasyprint.org')->toPdf()
    );
  }

  public function testCanRenderPdfFromText()
  {
    $this->runPdfAssertions(
      WeasyPrint::make(file_get_contents('https://weasyprint.org'))->toPdf()
    );
  }

  public function testCanRenderPdfFromView()
  {
    $this->runPdfAssertions(
      WeasyPrint::view('test-pdf')->toPdf()
    );
  }

  public function testCanRenderPngFromUrl()
  {
    $this->runPngAssertions(
      WeasyPrint::make('https://weasyprint.org')->toPng()
    );
  }

  public function testCanRenderPngFromText()
  {
    $this->runPngAssertions(
      WeasyPrint::make(file_get_contents('https://weasyprint.org'))->toPng()
    );
  }

  public function testCanRenderPngFromView()
  {
    $this->runPngAssertions(
      WeasyPrint::view('test-png')->toPng()
    );
  }

  public function testCanDownloadPdfOutput()
  {
    $this->runOutputFileAssertions(
      WeasyPrint::view('test-pdf')->download('test.pdf'),
      'application/pdf',
      'attachment; filename=test.pdf'
    );
  }

  public function testCanInlinePdfOutput()
  {
    $this->runOutputFileAssertions(
      WeasyPrint::view('test-pdf')->inline('test.pdf'),
      'application/pdf',
      'inline; filename=test.pdf'
    );
  }

  public function testCanDownloadPngOutput()
  {
    $this->runOutputFileAssertions(
      WeasyPrint::view('test-png')->download('test.png'),
      'image/png',
      'attachment; filename=test.png'
    );
  }

  public function testCanInlinePngOutput()
  {
    $this->runOutputFileAssertions(
      WeasyPrint::view('test-png')->inline('test.png'),
      'image/png',
      'inline; filename=test.png'
    );
  }

  public function testCanAcceptAdditionalOptions()
  {
    $output = WeasyPrint::view('test-pdf')
      ->setBaseUrl('https://weasyprint.org')
      ->setMediaType('screen')
      ->setPresentationalHints(true)
      ->toPdf();

    $this->runPdfAssertions($output);
  }

  public function testCanAcceptPngResolution()
  {
    $output = WeasyPrint::view('test-png')
      ->setResolution('300')
      ->toPng();

    $this->runPngAssertions($output);
  }

  public function testCanAcceptStylesheets()
  {
    $output = WeasyPrint::view('test-pdf')
      ->addStylesheet('https://fonts.googleapis.com/css?family=Manjari&display=swap')
      ->addStylesheet('https://fonts.googleapis.com/css?family=Roboto&display=swap')
      ->toPdf();

    $this->runPdfAssertions($output);
  }

  public function testCanAcceptAttachments()
  {
    $tempFilename = $this->writeTempFile('WeasyPrint');

    $output = WeasyPrint::view('test-pdf')
      ->addAttachment($tempFilename)
      ->toPdf();

    unlink($tempFilename);

    $this->runPdfAssertions($output);
  }
}
