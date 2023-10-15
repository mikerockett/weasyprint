<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Support\Renderable;
use Rockett\Pipeline\Contracts\PipelineContract;
use Rockett\Pipeline\Pipeline;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Commands\VersionCommand;
use WeasyPrint\Contracts\Factory;
use WeasyPrint\Exceptions\MissingSourceException;
use WeasyPrint\Objects\{Config, Output, Source};
use WeasyPrint\Pipeline\{BuildTraveler, Stages as Pipes};

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
    return app(Factory::class);
  }

  public function getWeasyPrintVersion(): string
  {
    $version = (new VersionCommand($this->config))->execute();

    return str_replace('WeasyPrint version ', '', $version);
  }

  public function setConfig(Config $config): self
  {
    $this->config = $config;

    return $this;
  }

  public function tapConfig(callable $callback): self
  {
    $callback($this->config);

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

  public function download(string $filename, array $headers = [], bool $inline = false): StreamedResponse
  {
    return $this->build()->download($filename, $headers, $inline);
  }

  public function inline(string $filename, array $headers = []): StreamedResponse
  {
    return $this->download($filename, $headers, true);
  }

  public function putFile(string $path, string $disk = null, array $options = []): bool
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
