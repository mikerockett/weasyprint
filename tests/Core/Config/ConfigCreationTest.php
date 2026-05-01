<?php

declare(strict_types=1);

use WeasyPrint\Enums\MediaType;
use WeasyPrint\Enums\PDFVariant;
use WeasyPrint\Enums\PDFVersion;
use WeasyPrint\Objects\Config;

describe('config creation', function (): void {
  it('can create with no parameters', function (): void {
    $config = new Config();

    expect($config)->toBeInstanceOf(Config::class);
    expect($config->binary)->toBeNull();
    expect($config->cachePrefix)->toBe('weasyprint_cache');
    expect($config->timeout)->toBe(60);
  });

  it('can create with custom parameters', function (): void {
    $config = new Config(
      binary: '/usr/bin/weasyprint',
      timeout: 120,
      dpi: 300,
    );

    expect($config->binary)->toBe('/usr/bin/weasyprint');
    expect($config->timeout)->toBe(120);
    expect($config->dpi)->toBe(300);
  });

  it('converts to array with expected structure', function (): void {
    $config = new Config(
      binary: '/usr/bin/weasyprint',
      timeout: 90,
    );

    $array = $config->toArray();

    expect($array)->toBeArray();
    expect($array['binary'])->toBe('/usr/bin/weasyprint');
    expect($array['timeout'])->toBe(90);
    expect($array['cachePrefix'])->toBe('weasyprint_cache');
  });

  it('expands enum from string values', function (): void {
    $config = new Config(
      mediaType: 'print',
      pdfVariant: 'pdf/a-1b',
      pdfVersion: '1.7',
    );

    expect($config->mediaType)->toBeInstanceOf(MediaType::class);
    expect($config->mediaType)->toBe(MediaType::PRINT);
    expect($config->pdfVariant)->toBeInstanceOf(PDFVariant::class);
    expect($config->pdfVariant)->toBe(PDFVariant::PDF_A_1B);
    expect($config->pdfVersion)->toBeInstanceOf(PDFVersion::class);
    expect($config->pdfVersion)->toBe(PDFVersion::VERSION_1_7);
  });

  it('serializes mediaType enum to string in toArray', function (): void {
    $config = new Config(mediaType: MediaType::SCREEN);
    $array = $config->toArray();

    expect($array['mediaType'])->toBe('screen');
  });
});
