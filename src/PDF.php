<?php

namespace WeasyPrint;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Enums\StreamMode;
use WeasyPrint\Objects\Config;
use WeasyPrint\Objects\Source;

abstract class PDF implements Responsable
{
  abstract public function source(): Source|Renderable|string;
  abstract public function filename(): string;

  /** @return array<string, string> */
  public function headers(): array
  {
    return [];
  }

  public function config(Config $config): void
  {
    // noop by default
  }

  public function defaultStreamMode(): StreamMode
  {
    return StreamMode::INLINE;
  }

  public function stream(StreamMode $mode): StreamedResponse
  {
    return Service::instance()
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
