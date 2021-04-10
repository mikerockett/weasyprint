<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Contracts\Factory;
use WeasyPrint\Enums\OutputType;
use WeasyPrint\Exceptions\{MissingOutputFileException, OutputStreamFailedException, SourceNotSetException};
use WeasyPrint\Objects\{Config, Output, Source};

class Service implements Factory
{
  protected Config $config;
  protected Source $source;
  protected OutputType $outputType;

  private function __construct(mixed ...$config)
  {
    $this->config = Config::new(...$config);
    $this->outputType = OutputType::pdf();
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

  public function to(OutputType $outputType): Factory
  {
    $service = clone $this;
    $service->outputType = $outputType;

    return $service;
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

  private function makeTemporaryFilename(): string|false
  {
    return tempnam(
      sys_get_temp_dir(),
      $this->config->getCachePrefix()
    );
  }

  public function build(): Output
  {
    if (!isset($this->source)) {
      throw new SourceNotSetException;
    }

    $inputPath = ($isUrl = $this->source->isUrl())
      ? $this->source->get()
      : $this->makeTemporaryFilename();

    $outputPath = $this->makeTemporaryFilename();
    $this->source->persistTemporaryFile($inputPath);

    $command = new Command(
      config: $this->config,
      outputType: $this->outputType,
      inputPath: $inputPath,
      outputPath: $outputPath,
      attachments: $this->source->getAttachments()
    );

    $command->execute();

    if (!$isUrl) {
      unlink($inputPath);
    }

    if (!is_file($outputPath)) {
      throw new MissingOutputFileException($outputPath);
    }

    if (!$output = file_get_contents($outputPath)) {
      throw new OutputStreamFailedException($outputPath);
    }

    unlink($outputPath);

    return Output::new($output, $this->outputType);
  }

  public function download(string $filename, array $headers = [], bool $inline = false): StreamedResponse
  {
    if (!$extension = Str::afterLast($filename, '.')) {
      $filename = Str::of($filename)
        ->trim('.')
        ->append($extension = $this->outputType->getValue());
    } else {
      $this->outputType = OutputType::from($extension);
    }

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
