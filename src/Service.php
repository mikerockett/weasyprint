<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Contracts\Support\Renderable;
use Rockett\Pipeline\{Contracts\PipelineContract, Pipeline};
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Contracts\Factory;
use WeasyPrint\Objects\{Config, Output, Source};
use WeasyPrint\Pipeline\{BuilderContainer, BuilderPipes as Pipes};

class Service implements Factory
{
  protected Config $config;
  protected Source $source;

  private function __construct(mixed ...$config)
  {
    $this->config = Config::new(...$config);
  }

  public static function new(mixed ...$config): Factory
  {
    return new static(...$config);
  }

  public static function createFromSource(Source|Renderable|string $source): Factory
  {
    return static::new()->prepareSource($source);
  }

  public function mergeConfig(mixed ...$config): Factory
  {
    $this->config = Config::new(...$config);

    return $this;
  }

  public function prepareSource(Source|Renderable|string $source): Factory
  {
    $this->source = match ($source instanceof Source) {
      true => $source,
      default => Source::new($source)
    };

    return $this;
  }

  public function getConfig(): Config
  {
    return $this->config;
  }

  public function sourceIsSet(): bool
  {
    return isset($this->source);
  }

  public function getSource(): Source
  {
    return $this->source;
  }

  public function addAttachment(string $pathToAttachment): Factory
  {
    $this->source->addAttachment($pathToAttachment);

    return $this;
  }

  public function build(): Output
  {
    $pipeline = (new Pipeline)
      ->pipe(new Pipes\EnsureSourceIsSet)
      ->pipe(new Pipes\SetInputPath)
      ->pipe(new Pipes\SetOutputPath)
      ->pipe(new Pipes\PersistTemporaryInput)
      ->pipe(new Pipes\PrepareCommand)
      ->pipe(new Pipes\ExecuteCommand)
      ->pipe(new Pipes\UnlinkTemporaryInput)
      ->pipe(new Pipes\PrepareOutput);

    return $this->processPipeline($pipeline)->getOutput();
  }

  protected function processPipeline(PipelineContract $pipeline): BuilderContainer
  {
    return $pipeline->process(new BuilderContainer($this));
  }

  public function download(string $filename, array $headers = [], bool $inline = false): StreamedResponse
  {
    return $this->build()->download($filename, $headers, $inline);
  }

  public function inline(string $filename, array $headers = []): StreamedResponse
  {
    return $this->download($filename, $headers, true);
  }

  public function putFile(string $path, ?string $disk = null, array $options = []): bool
  {
    return $this->build()->putFile($path, $disk, $options);
  }

  public function getData(): string
  {
    return $this->build()->getData();
  }
}
