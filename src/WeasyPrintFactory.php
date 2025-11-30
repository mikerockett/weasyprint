<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Contracts\Support\Renderable;
use Rockett\Pipeline\Builder\PipelineBuilder;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Commands\VersionCommand;
use WeasyPrint\Contracts\WeasyPrint;
use WeasyPrint\Enums\StreamMode;
use WeasyPrint\Exceptions\MissingSourceException;
use WeasyPrint\Objects\Config;
use WeasyPrint\Objects\Output;
use WeasyPrint\Objects\Source;
use WeasyPrint\Pipeline\BuildTraveler;
use WeasyPrint\Pipeline\Stages as Pipes;

class WeasyPrintFactory implements WeasyPrint
{
  public const SUPPORTED_VERSIONS = '^66.0';

  private Config $config;
  private Source $source;

  public function __construct(array $config = [])
  {
    $this->config = new Config(...$config);
  }

  public function getWeasyPrintVersion(): string
  {
    $version = (new VersionCommand($this->config))->execute();

    return str_replace('WeasyPrint version ', '', $version);
  }

  public function setConfig(Config $config): self
  {
    $this->config = $config;
    $this->config->runAssertions();

    return $this;
  }

  public function tapConfig(callable $callback): self
  {
    $callback($this->config);
    $this->config->runAssertions();

    return $this;
  }

  public function getConfig(): Config
  {
    return $this->config;
  }

  public function prepareSource(Source|Renderable|string $source): WeasyPrint
  {
    $this->source = match ($source instanceof Source) {
      true => $source,
      default => new Source($source),
    };

    return $this;
  }

  public function sourceIsSet(): bool
  {
    return isset($this->source);
  }

  public function getSource(): Source
  {
    return $this->source;
  }

  public function addAttachment(string $pathToAttachment): WeasyPrint
  {
    if (!$this->source) {
      throw new MissingSourceException();
    }

    $this->source->addAttachment($pathToAttachment);

    return $this;
  }

  public function build(): Output
  {
    return (new PipelineBuilder())
      ->add(new Pipes\AssertSupportedVersion())
      ->add(new Pipes\EnsureSourceIsSet())
      ->add(new Pipes\SetInputPath())
      ->add(new Pipes\SetOutputPath())
      ->add(new Pipes\PersistTemporaryInput())
      ->add(new Pipes\PrepareBuildCommand())
      ->add(new Pipes\Execute())
      ->add(new Pipes\UnlinkTemporaryInput())
      ->add(new Pipes\PrepareOutput())
      ->build()
      ->process(new BuildTraveler($this))
      ->getOutput();
  }

  public function stream(
    string $filename,
    array $headers = [],
    StreamMode $mode = StreamMode::INLINE,
  ): StreamedResponse {
    return $this->build()->stream($filename, $headers, $mode);
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
    return $this->build()->getData();
  }
}
