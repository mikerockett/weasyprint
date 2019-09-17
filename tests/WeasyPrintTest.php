<?php

use WeasyPrint\WeasyPrint;
use WeasyPrint\WeasyPrintProvider;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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

  private function runOutputFileAssertions($output, string $expectedDisposition)
  {
    /** @var ResponseHeaderBag */
    $headers = $output->headers;

    $isResponse = $headers instanceof ResponseHeaderBag;

    $this->assertTrue($isResponse);

    if ($isResponse) {
      $this->assertTrue($headers->get('content-type') === 'application/pdf');
      $this->assertTrue($headers->get('content-disposition') === $expectedDisposition);
    }
  }

  public function testCanRenderPdfFromText()
  {
    $pdf = WeasyPrint::make(file_get_contents('https://weasyprint.org'))->convert();

    $output = $pdf->get();

    $this->runPdfAssertions($output);
  }

  public function testCanRenderPdfFromView()
  {
    $pdf = WeasyPrint::view('test-pdf')->convert();

    $output = $pdf->get();

    $this->runPdfAssertions($output);
  }

  public function testCanRenderPngFromText()
  {
    $pdf = WeasyPrint::make(
      file_get_contents('https://weasyprint.org')
    )->convert('png');

    $output = $pdf->get();

    $this->runPngAssertions($output);
  }

  public function testCanRenderPngFromView()
  {
    $pdf = WeasyPrint::view('test-png')->convert('png');
    $output = $pdf->get();

    $this->runPngAssertions($output);
  }

  public function testCanDownloadOutput()
  {
    $pdf = WeasyPrint::view('test-pdf')->convert();
    $output = $pdf->download('test.pdf');

    $this->runOutputFileAssertions(
      $output, 'attachment; filename=test.pdf'
    );
  }

  public function testCanInlineOutput()
  {
    $pdf = WeasyPrint::view('test-pdf')->convert();
    $output = $pdf->inline('test.pdf');

    $this->runOutputFileAssertions(
      $output, 'inline; filename=test.pdf'
    );
  }

  public function testCanAcceptBaseUrl()
  {
    $pdf = WeasyPrint::view('test-pdf')->setBaseUrl('https://example.com')->convert();

    $output = $pdf->get();

    $this->runPdfAssertions($output);
  }
}
