<?php

declare(strict_types=1);

use WeasyPrint\Enums\PDFVariant;
use WeasyPrint\Enums\PDFVersion;

describe('environment config', function (): void {
  it('resolves PDFVariant enum from environment', function (): void {
    $this->scopeEnv(
      envKey: 'WEASYPRINT_PDF_VARIANT',
      envValue: 'pdf/a-1b',
      callback: fn(string $key) => expect(
        PDFVariant::fromEnvironment($key),
      )->toBe(PDFVariant::PDF_A_1B),
    );
  });

  it('returns null for PDFVariant when env not set', function (): void {
    expect(PDFVariant::fromEnvironment('NON_EXISTENT_KEY'))->toBeNull();
  });

  it('resolves PDFVersion enum from environment', function (): void {
    $this->scopeEnv(
      envKey: 'WEASYPRINT_PDF_VERSION',
      envValue: '1.7',
      callback: fn(string $key) => expect(
        PDFVersion::fromEnvironment($key),
      )->toBe(PDFVersion::VERSION_1_7),
    );
  });

  it('returns null for PDFVersion when env not set', function (): void {
    expect(PDFVersion::fromEnvironment('NON_EXISTENT_KEY'))->toBeNull();
  });
});
