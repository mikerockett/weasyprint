<?php

namespace WeasyPrint;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Enums\StreamMode;
use WeasyPrint\Objects\Source;

abstract class PDF implements Responsable
{
  protected StreamMode $streamMode = StreamMode::INLINE;
  protected string $filename;

  /** @var array<string, string> */
  protected array $headers = [];

  abstract public function source(): Source|Renderable|string;

  public function filename(): string
  {
    return $this->filename;
  }

  /** @return array<string, string> */
  public function headers(): array
  {
    return $this->headers;
  }

  public function streamMode(): StreamMode
  {
    return $this->streamMode;
  }

  public function toResponse($request): StreamedResponse
  {
    return Service::instance()
      ->prepareSource($this->source())
      ->stream($this->filename(), $this->headers(), $this->streamMode());
  }
}
