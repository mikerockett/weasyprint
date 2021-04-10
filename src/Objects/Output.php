<?php

declare(strict_types=1);

namespace WeasyPrint\Objects;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Enums\OutputType;

class Output
{
  private function __construct(
    protected string $data,
    protected OutputType $outputType
  ) {}

  public static function new(string $data, OutputType $outputType): static
  {
    return new static($data, $outputType);
  }

  private function getContentType(): string
  {
    return [
      'pdf' => 'application/pdf',
      'png' => 'image/png',
    ][$this->outputType->getValue()];
  }

  public function download(string $filename, array $headers = [], bool $inline = false): StreamedResponse
  {
    return Response::streamDownload(fn () => $this->data, $filename, array_merge($headers, [
      'Content-Type' => $this->getContentType()
    ]), $inline ? 'inline' : 'attachment');
  }

  public function inline(string $filename, array $headers = []): StreamedResponse
  {
    return $this->download($filename, $headers, true);
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
