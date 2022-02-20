<?php

declare(strict_types=1);

namespace WeasyPrint\Objects;

use Illuminate\Support\Facades\{Response, Storage};
use Symfony\Component\HttpFoundation\StreamedResponse;

class Output
{
  private function __construct(protected string $data)
  {
  }

  public static function new(string $data): static
  {
    return new static($data);
  }

  public function download(string $filename, array $headers = [], bool $inline = false): StreamedResponse
  {
    return Response::streamDownload(
      fn () => print $this->data,
      $filename,
      array_merge($headers, ['Content-Type' => 'application/pdf']),
      $inline ? 'inline' : 'attachment'
    );
  }

  public function inline(string $filename, array $headers = []): StreamedResponse
  {
    return $this->download($filename, $headers, inline: true);
  }

  public function putFile(string $path, ?string $disk = null, array $options = []): bool
  {
    return Storage::disk($disk)->put($path, $this->data, $options);
  }

  public function getData(): string
  {
    return $this->data;
  }
}
