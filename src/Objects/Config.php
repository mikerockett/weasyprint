<?php

declare(strict_types=1);

namespace WeasyPrint\Objects;

use Illuminate\Contracts\Support\Arrayable;
use WeasyPrint\Enums\{PDFVariant, PDFVersion};

class Config implements Arrayable
{
  private function __construct(
    protected ?string $binary = null,
    protected string $cachePrefix = 'weasyprint_cache',
    protected int $timeout = 3600,
    protected string $inputEncoding = 'utf-8',
    protected bool $presentationalHints = true,
    protected ?string $mediaType = null,
    protected ?string $baseUrl = null,
    protected ?array $stylesheets = null,
    protected array $processEnvironment = ['LC_ALL' => 'en_US.UTF-8'],
    protected ?PDFVariant $pdfVariant = null,
    protected ?PDFVersion $pdfVersion = null,
  ) {
  }

  public static function new(mixed ...$config): static
  {
    return new self(...array_merge(config('weasyprint'), $config));
  }

  public function getBinary(): ?string
  {
    return $this->binary;
  }

  public function getCachePrefix(): string
  {
    return $this->cachePrefix;
  }

  public function getTimeout(): int
  {
    return $this->timeout;
  }

  public function getInputEncoding(): string
  {
    return $this->inputEncoding;
  }

  public function usePresentationalHints(): bool
  {
    return $this->presentationalHints;
  }

  public function getMediaType(): ?string
  {
    return $this->mediaType;
  }

  public function getBaseUrl(): ?string
  {
    return $this->baseUrl;
  }

  public function getStylesheets(): ?array
  {
    return $this->stylesheets;
  }

  public function getProcessEnvironment(): array
  {
    return $this->processEnvironment;
  }

  public function getPdfVariant(): ?PDFVariant
  {
    return $this->pdfVariant;
  }

  public function getPdfVersion(): ?PDFVersion
  {
    return $this->pdfVersion;
  }

  public function toArray()
  {

    return [
      'binary' => $this->getBinary(),
      'cachePrefix' => $this->getCachePrefix(),
      'timeout' => $this->getTimeout(),
      'inputEncoding' => $this->getInputEncoding(),
      'presentationalHints' => $this->usePresentationalHints(),
      'mediaType' => $this->getMediaType(),
      'baseUrl' => $this->getBaseUrl(),
      'stylesheets' => $this->getStylesheets(),
      'processEnvironment' => $this->getProcessEnvironment(),
      'pdfVariant' => $this->getPdfVariant(),
      'pdfVersion' => $this->getPdfVersion(),
    ];
  }
}
