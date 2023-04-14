<?php

declare(strict_types=1);

namespace WeasyPrint\Objects;

use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use WeasyPrint\Enums\PDFVariant;

class Config implements Arrayable
{
  private function __construct(
    protected ?string $binary = null,
    protected string $cachePrefix = 'weasyprint_cache',
    protected int $timeout = 3600,
    protected string $inputEncoding = 'utf-8',
    protected bool $presentationalHints = true,
    protected string $optimizeSize = 'none',
    protected string|null $mediaType = null,
    protected string|null $baseUrl = null,
    protected array|null $stylesheets = null,
    protected array $processEnvironment = ['LC_ALL' => 'en_US.UTF-8'],
    protected PDFVariant|null $pdfVariant = null,
    protected string|null $pdfVersion = null,
  ) {
  }

  public static function new(mixed ...$config): static
  {
    $defaults = Container::getInstance()->make('config')->get('weasyprint');

    return new static(...array_merge($defaults, $config));
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

  public function getOptimizeSize(): string
  {
    return $this->optimizeSize;
  }

  public function getMediaType(): string|null
  {
    return $this->mediaType;
  }

  public function getBaseUrl(): string|null
  {
    return $this->baseUrl;
  }

  public function getStylesheets(): array|null
  {
    return $this->stylesheets;
  }

  public function getProcessEnvironment(): array
  {
    return $this->processEnvironment;
  }

  public function getPdfVariant(): PDFVariant|null
  {
    return $this->pdfVariant;
  }

  public function getPdfVersion(): string|null
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
      'optimizeSize' => $this->getOptimizeSize(),
      'mediaType' => $this->getMediaType(),
      'baseUrl' => $this->getBaseUrl(),
      'stylesheets' => $this->getStylesheets(),
      'processEnvironment' => $this->getProcessEnvironment(),
      'pdfVariant' => $this->getPdfVariant(),
      'pdfVersion' => $this->getPdfVersion(),
    ];
  }
}
