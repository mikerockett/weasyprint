<?php

declare(strict_types=1);

namespace WeasyPrint\Tests\Core\Support;

use Smalot\PdfParser\Parser;

trait PdfAssertions
{
  protected function writeTempFile(string $contents): string
  {
    $tempFilename = tempnam(sys_get_temp_dir(), 'weasyprint_test_');
    file_put_contents($tempFilename, $contents);

    return $tempFilename;
  }

  protected function assertValidPdf(string $data): void
  {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tempFile = $this->writeTempFile($data);
    $mime = finfo_file($finfo, $tempFile);

    expect($data)->not->toBeNull();
    expect($data)->not->toBeEmpty();
    expect($mime)->toEqual('application/pdf');

    $parser = new Parser();
    $document = $parser->parseFile($tempFile);

    expect($document->getDetails()['Producer'])->toStartWith('WeasyPrint');

    unlink($tempFile);
    expect(is_file($tempFile))->toBeFalse();
  }

  protected function assertPdfVersion(string $data, string $version): void
  {
    expect($data)->toStartWith("%PDF-{$version}");
  }
}
