<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use Rockett\Pipeline\Contracts\PipelineContract;
use Rockett\Pipeline\Pipeline;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Contracts\Factory;
use WeasyPrint\Enums\OutputType;
use WeasyPrint\Objects\{Config, Output, Source};
use WeasyPrint\Pipeline\{BuilderContainer, BuilderPipes as Pipes};

class Service implements Factory
{
  protected Config $config;
  protected Source $source;
  protected OutputType $outputType;

  private function __construct(mixed ...$config)
  {
    $this->config = Config::new(...$config);
    $this->outputType = OutputType::none();
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
    $service = clone $this;
    $service->config = Config::new(...$config);

    return $service;
  }

  public function prepareSource(Source|Renderable|string $source): Factory
  {
    $service = clone $this;

    $service->source = $source instanceof Source
      ? $source
      : Source::new($source);

    return $service;
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
    $service = clone $this;
    $service->source->addAttachment($pathToAttachment);

    return $service;
  }

  public function setOutputType(OutputType $outputType): Factory
  {
    $this->outputType = $outputType;

    return $this;
  }

  public function to(OutputType $outputType): Factory
  {
    return (clone $this)->setOutputType($outputType);
  }

  public function toPdf(): Factory
  {
    return $this->to(OutputType::pdf());
  }

  public function toPng(): Factory
  {
    return $this->to(OutputType::png());
  }

  public function getOutputType(): OutputType
  {
    return $this->outputType;
  }

  public function build(): Output
  {
    $pipeline = (new Pipeline)
      ->pipe(new Pipes\EnsureOutputTypeIsSet)
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
    return $pipeline->process(new BuilderContainer(clone $this));
  }

  protected function syncOutputTypeAndFilename(string $filename): string
  {
    if (!$extension = Str::afterLast($filename, '.')) {
      if ($this->outputType->is(OutputType::none())) {
        $this->outputType = OutputType::pdf();
      }

      return Str::of($filename)
        ->trim('.')
        ->append($extension = $this->outputType->getValue());
    }


    if ($this->outputType->is(OutputType::none())) {
      $this->outputType = OutputType::from($extension);
    }

    return $filename;
  }

  public function download(string $filename, array $headers = [], bool $inline = false): StreamedResponse
  {
    $filename = $this->syncOutputTypeAndFilename($filename);

    return $this->build()->download($filename, $headers, $inline);
  }

  public function inline(string $filename, array $headers = []): StreamedResponse
  {
    return $this->download($filename, $headers, true);
  }

  public function putFile(string $path, ?string $disk = null, array $options = []): bool
  {
    $path = $this->syncOutputTypeAndFilename($path);

    return $this->build()->putFile($path, $disk, $options);
  }

  public function getData(): string
  {
    return $this->build()->getData();
  }
}
