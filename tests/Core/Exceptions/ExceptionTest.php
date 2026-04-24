<?php

declare(strict_types=1);

use WeasyPrint\Exceptions\UnsupportedVersionException;
use WeasyPrint\Exceptions\TemporaryFileException;
use WeasyPrint\Exceptions\OutputReadFailedException;
use WeasyPrint\Exceptions\MissingOutputFileException;
use WeasyPrint\WeasyPrintFactory;

describe('exceptions', function (): void {
  test('UnsupportedVersionException contains version and constraint', function (): void {
    $exception = new UnsupportedVersionException('50.0');

    expect($exception->getMessage())->toContain('50.0');
    expect($exception->getMessage())->toContain(WeasyPrintFactory::SUPPORTED_VERSIONS);
  });

  test('TemporaryFileException contains input path', function (): void {
    $exception = new TemporaryFileException('/tmp/weasyprint_cache_abc123');

    expect($exception->getMessage())->toContain('/tmp/weasyprint_cache_abc123');
  });

  test('MissingOutputFileException contains output path', function (): void {
    $exception = new MissingOutputFileException('/tmp/output.pdf');

    expect($exception->getMessage())->toContain('/tmp/output.pdf');
  });

  test('OutputReadFailedException contains output path', function (): void {
    $exception = new OutputReadFailedException('/tmp/output.pdf');

    expect($exception->getMessage())->toContain('/tmp/output.pdf');
  });

  test('all exceptions extend RuntimeException', function (): void {
    expect(new UnsupportedVersionException('1.0'))->toBeInstanceOf(RuntimeException::class);
    expect(new TemporaryFileException('/tmp/x'))->toBeInstanceOf(RuntimeException::class);
    expect(new MissingOutputFileException('/tmp/x'))->toBeInstanceOf(RuntimeException::class);
    expect(new OutputReadFailedException('/tmp/x'))->toBeInstanceOf(RuntimeException::class);
  });
});
