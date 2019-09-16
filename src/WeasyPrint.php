<?php declare (strict_types = 1);

namespace WeasyPrint;

use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\Process\{ Process, Exception\ProcessFailedException };

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

  public function __construct($source)
  {
    $this->processBinary = config('weasyprint.binary', '/usr/local/bin/weasyprint');
    $this->processTimeout = config('weasyprint.timeout', 3600);
    $this->source = $source;
  }

  public static function make(): WeasyPrint
  {
    return new static(...func_get_args());
  }

  public function view($view, array $data = [], array $mergeData = []): WeasyPrint
  {
    return new static(view($view, $data, $mergeData));
  }

  public function setTimeout(int $timeout): WeasyPrint
  {
    $this->processTimeout = $timeout;
    return $this;
  }

  public function convert(string $outputMode = 'pdf', string $outputEncoding = 'utf8'): WeasyPrint
  {
    $this->outputMode = $outputMode;
    $this->outputEncoding = $outputEncoding;

    $this->preflight();
    $this->process();
    $this->fetch();

    return $this;
  }

  public function get(): string
  {
    return $this->output;
  }

  private function tempFilename()
  {
    return tempnam(sys_get_temp_dir(), config('weasyprint.cache_prefix', 'weasyprint-cache_'));
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

    $this->inputPath = $this->tempFilename();
    $this->outputPath = $this->tempFilename();

    $this->writeTempInputFile();
  }

  private function process(): void
  {
    $process = new Process(
      [
        $this->processBinary,
        $this->inputPath,
        $this->outputPath,
        '-f', $this->outputMode,
        '-e', $this->outputEncoding,
      ],
      null,
      ['LC_ALL' => 'en_US.UTF-8']
    );

    $process->setTimeout($this->processTimeout);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    unlink($this->inputPath);
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
