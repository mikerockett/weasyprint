<?php

declare(strict_types=1);

namespace WeasyPrint\Contracts;

use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Enums\StreamMode;
use WeasyPrint\Objects\Config;
use WeasyPrint\Objects\Output;
use WeasyPrint\Objects\Source;

interface Factory
{
  public function getWeasyPrintVersion(): string;
  public function setConfig(Config $config): self;
  public function tapConfig(callable $callback): self;
  public function getConfig(): Config;
  public function prepareSource(Source|Renderable|string $source): self;
  public function getSource(): Source;
  public function addAttachment(string $pathToAttachment): Factory;
  public function build(): Output;
  public function stream(string $filename, array $headers = [], StreamMode $mode = StreamMode::INLINE): StreamedResponse;
  public function download(string $filename, array $headers = []): StreamedResponse;
  public function inline(string $filename, array $headers = []): StreamedResponse;
  public function putFile(string $path, string|null $disk = null, array $options = []): bool;
  public function getData(): string;
}
