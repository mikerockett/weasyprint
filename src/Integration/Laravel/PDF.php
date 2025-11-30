<?php

declare(strict_types=1);

namespace WeasyPrint\Integration\Laravel;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Contracts\WeasyPrint;
use WeasyPrint\Enums\StreamMode;
use WeasyPrint\Objects\Config;
use WeasyPrint\Objects\Source;

abstract class PDF implements Responsable
{
  abstract public function source(): Source|Renderable|string;
  abstract public function filename(): string;

  public function config(Config $config): void {}

  /** @return array<string, string> */
  public function headers(): array
  {
    return [];
  }

  public function defaultStreamMode(): StreamMode
  {
    return StreamMode::INLINE;
  }

  public function stream(StreamMode $mode): StreamedResponse
  {
    return app(WeasyPrint::class)
      ->tapConfig($this->config(...))
      ->prepareSource($this->source())
      ->stream($this->filename(), $this->headers(), $mode);
  }

  public function download(): StreamedResponse
  {
    return $this->stream(StreamMode::DOWNLOAD);
  }

  public function inline(): StreamedResponse
  {
    return $this->stream(StreamMode::INLINE);
  }

  public function toResponse($request): StreamedResponse
  {
    return $this->stream($this->defaultStreamMode());
  }
}
