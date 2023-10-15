<?php

declare(strict_types=1);

namespace WeasyPrint\Objects;

use Illuminate\Contracts\Support\Arrayable;
use WeasyPrint\Enums\{PDFVariant, PDFVersion};

final class Config implements Arrayable
{
  public function __construct(
    public ?string $binary = null,
    public string $cachePrefix = 'weasyprint_cache',
    public int $timeout = 3600,
    public string $inputEncoding = 'utf-8',
    public bool $presentationalHints = true,
    public ?string $mediaType = null,
    public ?string $baseUrl = null,
    public array $stylesheets = [],
    public array $processEnvironment = ['LC_ALL' => 'en_US.UTF-8'],
    public PDFVariant|string|null $pdfVariant = null,
    public PDFVersion|string|null $pdfVersion = null,
  ) {
    $this->expandEnums();
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
    ];
  }
}
