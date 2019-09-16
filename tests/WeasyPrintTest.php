<?php

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use WeasyPrint\WeasyPrint;
use WeasyPrint\WeasyPrintProvider;

class WeasyPrintTest extends Orchestra\Testbench\TestCase
{
  protected function getPackageProviders($app)
  {
    return [WeasyPrintProvider::class];
  }

  public function testWeasyPrint()
  {
    $pdf = WeasyPrint::make(file_get_contents('https://google.com'))->convert();
    $output = $pdf->get();

    $this->assertNotNull($output);
    $this->assertNotEmpty($output);
    $this->assertStringStartsWith('%PDF-1.5', $output);
  }
}
