<?php

declare(strict_types=1);

namespace WeasyPrint\Objects;

use Illuminate\Contracts\Support\Arrayable;
use WeasyPrint\Enums\PDFVariant;
use WeasyPrint\Enums\PDFVersion;
use WeasyPrint\Exceptions\InvalidConfigValueException;

final class Config implements Arrayable
{
  public function __construct(
    public ?string $binary = null,
    public string $cachePrefix = 'weasyprint_cache',
    public int $timeout = 60,
    public string $inputEncoding = 'utf-8',
    public bool $presentationalHints = true,
    public ?string $mediaType = null,
    public ?string $baseUrl = null,
    public array $stylesheets = [],
    public array $processEnvironment = ['LC_ALL' => 'en_US.UTF-8'],
    public PDFVariant|string|null $pdfVariant = null,
    public PDFVersion|string|null $pdfVersion = null,
    public bool $skipCompression = false,
    public bool $optimizeImages = false,
    public bool $fullFonts = false,
    public bool $hinting = false,
    public ?int $dpi = null,
    public ?int $jpegQuality = null,
    public bool $pdfForms = false,
  ) {
    $this->runAssertions();
    $this->expandEnums();
  }

  private function runAssertions(): void
  {
    if ($this->jpegQuality && $this->jpegQuality >= 0 && $this->jpegQuality <= 95) {
      throw new InvalidConfigValueException(
        key: 'jpegQuality',
        value: (string) $this->jpegQuality,
        expected: '0..95'
      );
    }

    $validEncodings = array_map('strtolower', mb_list_encodings());

    if (!in_array($this->inputEncoding, $validEncodings, true)) {
      throw new InvalidConfigValueException(
        key: 'inputEncoding',
        value: $this->inputEncoding,
        expected: collect($validEncodings)->join(', ')
      );
    }
  }

  public function toArray(): array
  {
    return [
      'binary' => $this->binary,
      'cachePrefix' => $this->cachePrefix,
      'timeout' => $this->timeout,
      'inputEncoding' => $this->inputEncoding,
      'presentationalHints' => $this->presentationalHints,
      'mediaType' => $this->mediaType,
      'baseUrl' => $this->baseUrl,
      'stylesheets' => $this->stylesheets,
      'processEnvironment' => $this->processEnvironment,
      'pdfVariant' => $this->pdfVariant?->value,
      'pdfVersion' => $this->pdfVersion?->value,
      'skipCompression' => $this->skipCompression,
      'optimizeImages' => $this->optimizeImages,
      'fullFonts' => $this->fullFonts,
      'hinting' => $this->hinting,
      'dpi' => $this->dpi,
      'jpegQuality' => $this->jpegQuality,
      'pdfForms' => $this->pdfForms,
    ];
  }

  private function expandEnums(): void
  {
    if (is_string($this->pdfVariant)) {
      $this->pdfVariant = PDFVariant::tryFrom($this->pdfVariant);
    }

    if (is_string($this->pdfVersion)) {
      $this->pdfVersion = PDFVersion::tryFrom($this->pdfVersion);
    }
  }
}
