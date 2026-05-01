<?php

declare(strict_types=1);

use WeasyPrint\Exceptions\AttachmentNotFoundException;
use WeasyPrint\Exceptions\BinaryNotFoundException;
use WeasyPrint\Exceptions\InvalidConfigValueException;
use WeasyPrint\Exceptions\MissingOutputFileException;
use WeasyPrint\Exceptions\OutputReadFailedException;
use WeasyPrint\Exceptions\SourceNotSetException;
use WeasyPrint\Exceptions\TemporaryFileException;
use WeasyPrint\Exceptions\UnsupportedVersionException;
use WeasyPrint\Exceptions\WeasyPrintException;
use WeasyPrint\WeasyPrintFactory;

describe('exceptions', function (): void {
  it('includes version and constraint in UnsupportedVersionException', function (): void {
    $exception = new UnsupportedVersionException('50.0');

    expect($exception->getMessage())->toContain('50.0');
    expect($exception->getMessage())->toContain(WeasyPrintFactory::SUPPORTED_VERSIONS);
  });

  it('includes input path in TemporaryFileException', function (): void {
    $exception = new TemporaryFileException('/tmp/weasyprint_cache_abc123');

    expect($exception->getMessage())->toContain('/tmp/weasyprint_cache_abc123');
  });

  it('includes output path in MissingOutputFileException', function (): void {
    $exception = new MissingOutputFileException('/tmp/output.pdf');

    expect($exception->getMessage())->toContain('/tmp/output.pdf');
  });

  it('includes output path in OutputReadFailedException', function (): void {
    $exception = new OutputReadFailedException('/tmp/output.pdf');

    expect($exception->getMessage())->toContain('/tmp/output.pdf');
  });

  it('extends RuntimeException for all exception types', function (): void {
    expect(new UnsupportedVersionException('1.0'))->toBeInstanceOf(RuntimeException::class);
    expect(new TemporaryFileException('/tmp/x'))->toBeInstanceOf(RuntimeException::class);
    expect(new MissingOutputFileException('/tmp/x'))->toBeInstanceOf(RuntimeException::class);
    expect(new OutputReadFailedException('/tmp/x'))->toBeInstanceOf(RuntimeException::class);
  });

  it('implements WeasyPrintException interface for all exception types', function (): void {
    expect(new AttachmentNotFoundException('/tmp/x'))->toBeInstanceOf(WeasyPrintException::class);
    expect(new BinaryNotFoundException())->toBeInstanceOf(WeasyPrintException::class);
    expect(new InvalidConfigValueException('key', 'val', 'exp'))->toBeInstanceOf(WeasyPrintException::class);
    expect(new MissingOutputFileException('/tmp/x'))->toBeInstanceOf(WeasyPrintException::class);
    expect(new OutputReadFailedException('/tmp/x'))->toBeInstanceOf(WeasyPrintException::class);
    expect(new SourceNotSetException())->toBeInstanceOf(WeasyPrintException::class);
    expect(new TemporaryFileException('/tmp/x'))->toBeInstanceOf(WeasyPrintException::class);
    expect(new UnsupportedVersionException('1.0'))->toBeInstanceOf(WeasyPrintException::class);
  });

  it('includes reason in TemporaryFileException when provided', function (): void {
    $exception = new TemporaryFileException('/tmp/x', 'path must be within the system temp directory');

    expect($exception->getMessage())->toContain('path must be within the system temp directory');
    expect($exception->getMessage())->toContain('/tmp/x');
  });
});
