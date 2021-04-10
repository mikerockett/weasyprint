<?php

declare(strict_types=1);

namespace WeasyPrint\Objects;

use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;

class Config implements Arrayable
{
  private function __construct(
    protected string $binary = '/usr/local/bin/weasyprint',
    protected string $cachePrefix = 'weasyprint_cache',
    protected int $timeout = 3600,
    protected string $inputEncoding = 'utf-8',
    protected bool $presentationalHints = true,
    protected bool $optimizeImages = false,
    protected int|null $resolution = null,
    protected string|null $mediaType = null,
    protected string|null $baseUrl = null,
    protected array|null $stylesheets = null,
  ) {
  }

  public static function new(mixed ...$configurationOptions): static
  {
    $defaults = Container::getInstance()->make('config')->get('weasyprint');

    return new static(...array_merge($defaults, $configurationOptions));
  }

  public function getBinary(): string
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

  public function shouldOptimizeImages(): bool
  {
    return $this->optimizeImages;
  }

  public function getResolution(): int|null
  {
    return $this->resolution;
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

  public function toArray()
  {
    return [
      'binary' => $this->getBinary(),
      'cachePrefix' => $this->getCachePrefix(),
      'timeout' => $this->getTimeout(),
      'inputEncoding' => $this->getInputEncoding(),
      'presentationalHints' => $this->usePresentationalHints(),
      'optimizeImages' => $this->shouldOptimizeImages(),
      'resolution' => $this->getResolution(),
      'mediaType' => $this->getMediaType(),
      'baseUrl' => $this->getBaseUrl(),
      'stylesheets' => $this->getStylesheets(),
    ];
  }
}
