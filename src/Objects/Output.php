<?php

declare(strict_types=1);

namespace WeasyPrint\Objects;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Enums\StreamMode;

final class Output
{
  public function __construct(
    protected string $data
  ) {}

  public function stream(string $filename, array $headers = [], StreamMode $mode = StreamMode::INLINE): StreamedResponse
  {
    return Response::streamDownload(
      callback: fn() => print $this->data,
      name: $filename,
      headers: array_merge($headers, [
        'Content-Type' => 'application/pdf'
      ]),
      disposition: $mode->disposition()
    );
  }

  public function download(string $filename, array $headers = []): StreamedResponse
  {
    return $this->stream($filename, $headers, StreamMode::DOWNLOAD);
  }

  public function inline(string $filename, array $headers = []): StreamedResponse
  {
    return $this->stream($filename, $headers, StreamMode::INLINE);
  }

  public function putFile(string $path, string|null $disk = null, array $options = []): bool
  {
    return Storage::disk($disk)->put($path, $this->data, $options);
  }

  public function getData(): string
  {
    return $this->data;
  }
}
