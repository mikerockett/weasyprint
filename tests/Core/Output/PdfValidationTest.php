<?php

declare(strict_types=1);

use WeasyPrint\Enums\PDFVersion;
use WeasyPrint\Objects\Config;
use WeasyPrint\WeasyPrintService;
use WeasyPrint\Tests\Fixtures\SampleHtml;

describe('pdf validation', function (): void {
  test('pdf has correct mime type', function (): void {
    $service = new WeasyPrintService();
    $service->prepareSource(SampleHtml::simple());
    $data = $service->getData();

    $tempFile = $this->writeTempFile($data);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tempFile);

    expect($mime)->toBe('application/pdf');
    unlink($tempFile);
  });

  test('pdf contains producer metadata', function (): void {
    $service = new WeasyPrintService();
    $service->prepareSource(SampleHtml::simple());
    $data = $service->getData();

    // Use PDF parser to verify producer
    $tempFile = $this->writeTempFile($data);
    $parser = new Smalot\PdfParser\Parser();
    $document = $parser->parseFile($tempFile);

    expect($document->getDetails()['Producer'])->toStartWith('WeasyPrint');
    unlink($tempFile);
  });

  test('can set pdf version', function (): void {
    $service = new WeasyPrintService();
    $service->setConfig(new Config(pdfVersion: PDFVersion::VERSION_1_7));
    $service->prepareSource(SampleHtml::simple());
    $data = $service->getData();

    $this->assertPdfVersion($data, '1.7');
  });
});
