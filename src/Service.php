<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Renderable;
use Rockett\Pipeline\Contracts\PipelineContract;
use Rockett\Pipeline\Pipeline;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Commands\VersionCommand;
use WeasyPrint\Contracts\Factory;
use WeasyPrint\Enums\StreamMode;
use WeasyPrint\Exceptions\MissingSourceException;
use WeasyPrint\Objects\Config;
use WeasyPrint\Objects\Output;
use WeasyPrint\Objects\Source;
use WeasyPrint\Pipeline\BuildTraveler;
use WeasyPrint\Pipeline\Stages as Pipes;

class Service implements Factory
{
  private Config $config;
  private Source $source;

  public function __construct(Repository $config)
  {
    $this->config = new Config(...$config->get('weasyprint'));
  }

  public static function instance(): self
  {
    return Container::getInstance()->make(Factory::class);
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

  public function prepareSource(Source|Renderable|string $source): Factory
  {
    $this->source = match ($source instanceof Source) {
      true => $source,
      default => new Source($source)
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

  public function addAttachment(string $pathToAttachment): Factory
  {
    if (!$this->source) {
      throw new MissingSourceException();
    }

    $this->source->addAttachment($pathToAttachment);

    return $this;
  }

  public function build(): Output
  {
    return $this->processPipeline(
      new Pipeline(
        null,
        new Pipes\AssertSupportedVersion(),
        new Pipes\EnsureSourceIsSet(),
        new Pipes\SetInputPath(),
        new Pipes\SetOutputPath(),
        new Pipes\PersistTemporaryInput(),
        new Pipes\PrepareBuildCommand(),
        new Pipes\Execute(),
        new Pipes\UnlinkTemporaryInput(),
        new Pipes\PrepareOutput(),
      )
    )->getOutput();
  }

  public function stream(string $filename, array $headers = [], StreamMode $mode = StreamMode::INLINE): StreamedResponse
  {
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

  public function putFile(string $path, string|null $disk = null, array $options = []): bool
  {
    return $this->build()->putFile($path, $disk, $options);
  }

  public function getData(): string
  {
    return $this->build()->getData();
  }

  private function processPipeline(PipelineContract $pipeline): BuildTraveler
  {
    return $pipeline->process(new BuildTraveler($this));
  }
}
