<?php

declare(strict_types=1);

namespace WeasyPrint\Testing;

use Illuminate\Contracts\Support\Renderable;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Contracts\WeasyPrint;
use WeasyPrint\Enums\StreamMode;
use WeasyPrint\Exceptions\SourceNotSetException;
use WeasyPrint\Objects\Config;
use WeasyPrint\Objects\Output;
use WeasyPrint\Objects\Source;

class FakeWeasyPrint implements WeasyPrint
{
  private Config $config;
  private ?Source $source = null;
  private array $xmpMetadata = [];

  private array $calls = [];

  public function __construct(array|Config $config = [])
  {
    $this->config = $config instanceof Config
      ? $config
      : new Config(...$config);
  }

  public function getWeasyPrintVersion(): string
  {
    $this->recordCall('getWeasyPrintVersion');

    return '68.0';
  }

  public function setConfig(Config $config): self
  {
    $this->config = $config;
    $this->recordCall('setConfig', ['config' => $config]);

    return $this;
  }

  public function tapConfig(callable $callback): self
  {
    $callback($this->config);
    $this->recordCall('tapConfig');

    return $this;
  }

  public function getConfig(): Config
  {
    return $this->config;
  }

  public function prepareSource(Source|Renderable|string $source): self
  {
    $this->source = match ($source instanceof Source) {
      true => $source,
      default => new Source($source),
    };

    $this->recordCall('prepareSource', ['source' => $source]);

    return $this;
  }

  public function sourceIsSet(): bool
  {
    return $this->source !== null;
  }

  public function getSource(): Source
  {
    return $this->source;
  }

  public function addXmpMetadata(string $path): self
  {
    $this->xmpMetadata[] = $path;
    $this->recordCall('addXmpMetadata', ['path' => $path]);

    return $this;
  }

  public function getXmpMetadata(): array
  {
    return $this->xmpMetadata;
  }

  public function addAttachment(string $pathToAttachment, ?string $relationship = null): self
  {
    if ($this->source === null) {
      throw new SourceNotSetException();
    }

    $this->source->addAttachment($pathToAttachment, $relationship);
    $this->recordCall('addAttachment', [
      'path' => $pathToAttachment,
      'relationship' => $relationship,
    ]);

    return $this;
  }

  public function build(): Output
  {
    $this->recordCall('build');

    return new Output('%PDF-1.4 fake');
  }

  public function stream(
    string $filename,
    array $headers = [],
    StreamMode $mode = StreamMode::INLINE,
  ): StreamedResponse {
    $this->recordCall('stream', [
      'filename' => $filename,
      'headers' => $headers,
      'mode' => $mode,
    ]);

    return $this->makeFakeResponse($filename, $headers, $mode);
  }

  public function download(string $filename, array $headers = []): StreamedResponse
  {
    $this->recordCall('download', [
      'filename' => $filename,
      'headers' => $headers,
    ]);

    return $this->makeFakeResponse($filename, $headers, StreamMode::DOWNLOAD);
  }

  public function inline(string $filename, array $headers = []): StreamedResponse
  {
    $this->recordCall('inline', [
      'filename' => $filename,
      'headers' => $headers,
    ]);

    return $this->makeFakeResponse($filename, $headers, StreamMode::INLINE);
  }

  public function getData(): string
  {
    $this->recordCall('getData');

    return '%PDF-1.4 fake';
  }

  public function assertBuilt(int $times = 1): self
  {
    Assert::assertCount($times, $this->getCalls('build'), "Expected build() to be called {$times} time(s).");

    return $this;
  }

  public function assertNotBuilt(): self
  {
    Assert::assertEmpty($this->getCalls('build'), 'Expected build() not to be called.');

    return $this;
  }

  public function assertSourcePrepared(?callable $callback = null): self
  {
    $calls = $this->getCalls('prepareSource');
    Assert::assertNotEmpty($calls, 'Expected prepareSource() to be called.');

    if ($callback) {
      foreach ($calls as $call) {
        $callback($call['source']);
      }
    }

    return $this;
  }

  public function assertDownloaded(?string $filename = null): self
  {
    $calls = $this->getCalls('download');
    Assert::assertNotEmpty($calls, 'Expected download() to be called.');

    if ($filename) {
      $filenames = array_column($calls, 'filename');
      Assert::assertContains($filename, $filenames, "Expected download() to be called with filename '{$filename}'.");
    }

    return $this;
  }

  public function assertInlined(?string $filename = null): self
  {
    $calls = $this->getCalls('inline');
    Assert::assertNotEmpty($calls, 'Expected inline() to be called.');

    if ($filename) {
      $filenames = array_column($calls, 'filename');
      Assert::assertContains($filename, $filenames, "Expected inline() to be called with filename '{$filename}'.");
    }

    return $this;
  }

  public function assertStreamed(?string $filename = null, ?StreamMode $mode = null): self
  {
    $calls = $this->getCalls('stream');
    Assert::assertNotEmpty($calls, 'Expected stream() to be called.');

    if ($filename) {
      $filenames = array_column($calls, 'filename');
      Assert::assertContains($filename, $filenames, "Expected stream() to be called with filename '{$filename}'.");
    }

    if ($mode) {
      $modes = array_column($calls, 'mode');
      Assert::assertContains($mode, $modes, "Expected stream() to be called with mode {$mode->name}.");
    }

    return $this;
  }

  public function assertAttachmentAdded(?string $path = null): self
  {
    $calls = $this->getCalls('addAttachment');
    Assert::assertNotEmpty($calls, 'Expected addAttachment() to be called.');

    if ($path) {
      $paths = array_column($calls, 'path');
      Assert::assertContains($path, $paths, "Expected addAttachment() to be called with path '{$path}'.");
    }

    return $this;
  }

  public function assertXmpMetadataAdded(?string $path = null): self
  {
    $calls = $this->getCalls('addXmpMetadata');
    Assert::assertNotEmpty($calls, 'Expected addXmpMetadata() to be called.');

    if ($path) {
      $paths = array_column($calls, 'path');
      Assert::assertContains($path, $paths, "Expected addXmpMetadata() to be called with path '{$path}'.");
    }

    return $this;
  }

  public function assertNothingBuilt(): self
  {
    return $this->assertNotBuilt();
  }

  private function recordCall(string $method, array $args = []): void
  {
    $this->calls[] = ['method' => $method, ...$args];
  }

  private function getCalls(string $method): array
  {
    return array_values(array_filter(
      $this->calls,
      fn(array $call) => $call['method'] === $method,
    ));
  }

  private function makeFakeResponse(string $filename, array $headers, StreamMode $mode): StreamedResponse
  {
    $response = new StreamedResponse(
      fn() => print '',
      status: 200,
      headers: array_merge($headers, ['Content-Type' => 'application/pdf']),
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
}
