<?php declare (strict_types = 1);

namespace WeasyPrint;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class WeasyPrint
{
  private $source;
  private $inputPath;
  private $output;
  private $outputMode;
  private $outputEncoding;
  private $outputPath;
  private $processBinary;
  private $processTimeout;
  private $baseUrl;

  private $stylesheets = [];

  public function __construct($source)
  {
    $this->processBinary = config('weasyprint.binary');
    $this->processTimeout = config('weasyprint.timeout');
    $this->source = $source;
  }

  public static function version(): string
  {
    $process = new Process([config('weasyprint.binary'), '--version']);

    $process->setTimeout(config('weasyprint.timeout'));
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    return $process->getOutput();
  }

  public static function make(): WeasyPrint
  {
    return new static(...func_get_args());
  }

  public static function view($view, array $data = [], array $mergeData = []): WeasyPrint
  {
    return new static(view($view, $data, $mergeData));
  }

  public function setTimeout(int $timeout): WeasyPrint
  {
    $this->processTimeout = $timeout;

    return $this;
  }

  public function setBaseUrl(string $url): WeasyPrint
  {
    $this->baseUrl = $url;

    return $this;
  }

  public function addStylesheet(string $path): WeasyPrint
  {
    array_push($this->stylesheets, $path);

    return $this;
  }

  public function convert(string $outputMode = 'pdf', string $outputEncoding = 'utf8'): WeasyPrint
  {
    if ($this->output) {
      return $this;
    }

    $this->outputMode = $outputMode;
    $this->outputEncoding = $outputEncoding;

    $this->preflight();
    $this->process();
    $this->fetch();

    return $this;
  }

  public function get(): string
  {
    $this->convert();

    return $this->output;
  }

  public function toPdf(): string
  {
    return $this->get();
  }

  public function toPng(): string
  {
    return $this->convert('png')->get();
  }

  private function responseHeaders(bool $inline, string $filename): array
  {
    $inline = $inline ? 'inline' : 'attachment';

    return [
      'Content-Disposition' => "$inline; filename=$filename",
      'Content-Type' => 'application/pdf',
    ];
  }

  public function download(string $filename): Response
  {
    return response(
      $this->get(), 200, $this->responseHeaders(false, $filename)
    );
  }

  public function inline(string $filename): Response
  {
    return response(
      $this->get(), 200, $this->responseHeaders(true, $filename)
    );
  }

  private function sourceIsUrl(): bool
  {
    return filter_var($this->source, FILTER_VALIDATE_URL) !== false;
  }

  private function tempFilename()
  {
    return tempnam(
      sys_get_temp_dir(),
      config('weasyprint.cache_prefix', 'weasyprint-cache_')
    );
  }

  private function writeTempInputFile(): void
  {
    if (!file_put_contents($this->inputPath, $this->source)) {
      throw new \Exception('Unable to write temporary input file.');
    }
  }

  private function preflight(): void
  {
    if ($this->source instanceof Renderable) {
      $this->source = $this->source->render();
    }

    $this->inputPath = $isUrl = $this->sourceIsUrl()
      ? $this->source
      : $this->tempFilename();

    $this->outputPath = $this->tempFilename();

    if (!$isUrl) {
      $this->writeTempInputFile();
    }
  }

  private function process(): void
  {
    $command = [
      $this->processBinary,
      $this->inputPath,
      $this->outputPath,
      '--format', $this->outputMode,
      '--encoding', $this->outputEncoding,
    ];

    if ($this->baseUrl) {
      array_push($command, '--base-url', $this->baseUrl);
    }

    if (count($this->stylesheets)) {
      foreach ($this->stylesheets as $stylesheet) {
        array_push($command, '--stylesheet', $stylesheet);
      }
    }

    $process = new Process($command, null, ['LC_ALL' => 'en_US.UTF-8']);
    $process->setTimeout($this->processTimeout)->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    if (!$this->sourceIsUrl()) {
      unlink($this->inputPath);
    }
  }

  private function fetch(): void
  {
    if (!is_file($this->outputPath)) {
      throw new \Exception('The output file was not created by the processor.');
    }

    $this->output = file_get_contents($this->outputPath);

    if (!is_string($this->output)) {
      throw new \Exception('The output file could not be fetched.');
    }

    unlink($this->outputPath);
  }
}
