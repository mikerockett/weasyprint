<?php

declare(strict_types=1);

namespace WeasyPrint\Objects;

use Stringable;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Enums\StreamMode;

final class Output implements Stringable
{
  public function __construct(
    protected string $data,
  ) {}

  public function stream(
    string $filename,
    array $headers = [],
    StreamMode $mode = StreamMode::INLINE,
  ): StreamedResponse {
    $response = new StreamedResponse(
      fn() => print $this->data,
      status: 200,
      headers: array_merge(
        $headers,
        ['Content-Type' => 'application/pdf'],
      ),
    );

    $response->headers->set(
      'Content-Disposition',
      $response->headers->makeDisposition(
        $mode->disposition(),
        $filename,
        filenameFallback: str_replace('%', '', $filename),
      ),
    );

    return $response;
  }

  public function download(string $filename, array $headers = []): StreamedResponse
  {
    return $this->stream($filename, $headers, StreamMode::DOWNLOAD);
  }

  public function inline(string $filename, array $headers = []): StreamedResponse
  {
    return $this->stream($filename, $headers, StreamMode::INLINE);
  }

  public function getData(): string
  {
    return $this->data;
  }

  public function __toString(): string
  {
    return $this->getData();
  }
}
