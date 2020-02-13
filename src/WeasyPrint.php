<?php declare (strict_types = 1);

namespace WeasyPrint;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use WeasyPrint\Exceptions\InvalidOutputModeException;

class WeasyPrint
{
  private $source;
  private $isUrl;
  private $inputPath;
  private $outputMode;
  private $outputPath;
  private $output;

  private $baseUrl;
  private $resolution;
  private $mediaType;
  private $presentationalHints;

  private $processBinary;
  private $processTimeout;

  private $command = [];
  private $outputEncoding = 'utf-8';
  private $attachments = [];
  private $stylesheets = [];

  public function __construct($source)
  {
    $this->processBinary = config('weasyprint.binary');
    $this->processTimeout = config('weasyprint.timeout');
    $this->source = $source;
    $this->isUrl = $this->sourceIsUrl($source);
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

  public function setBaseUrl(string $baseUrl): WeasyPrint
  {
    $this->baseUrl = $baseUrl;

    return $this;
  }

  public function setResolution(string $resolution): WeasyPrint
  {
    $this->resolution = $resolution;

    return $this;
  }

  public function setMediaType(string $mediaType): WeasyPrint
  {
    $this->mediaType = $mediaType;

    return $this;
  }

  public function setAttachment(string $attachment): WeasyPrint
  {
    $this->attachment = $attachment;

    return $this;
  }

  public function setPresentationalHints(bool $presentationalHints): WeasyPrint
  {
    $this->presentationalHints = $presentationalHints;

    return $this;
  }

  public function setOutputEncoding(string $outputEncoding): WeasyPrint
  {
    $this->outputEncoding = $outputEncoding;

    return $this;
  }

  public function addStylesheet(string $path): WeasyPrint
  {
    array_push($this->stylesheets, $path);

    return $this;
  }

  public function addAttachment(string $path): WeasyPrint
  {
    array_push($this->attachments, $path);

    return $this;
  }

  private function convertTo(string $outputMode = 'pdf'): WeasyPrint
  {
    throw_if(
      !in_array($outputMode, ['png', 'pdf']),
      new InvalidOutputModeException
    );

    if (!$this->output) {
      $this->outputMode = $outputMode;

      $this->preflight();
      $this->process();
      $this->fetch();
    }

    return $this;
  }

  private function getOutput(): string
  {
    return $this->output;
  }

  public function toPdf(): string
  {
    return $this->convertTo('pdf')->getOutput();
  }

  public function toPng(): string
  {
    return $this->convertTo('png')->getOutput();
  }

  private function getDispositionType(bool $inline): string
  {
    return $inline ? 'inline' : 'attachment';
  }

  private function getMime(string $outputMode): string
  {
    return [
      'pdf' => 'application/pdf',
      'png' => 'image/png',
    ][$outputMode];
  }

  private function responseHeaders(string $filename, string $outputMode, bool $inline = false): array
  {
    $dispositionType = $this->getDispositionType($inline);
    $mime = $this->getMime($outputMode);

    return [
      'Content-Disposition' => "$dispositionType; filename=$filename",
      'Content-Type' => $mime,
    ];
  }

  private function makeFileResponse(string $filename, bool $inline): Response
  {
    $outputMode = substr(strrchr($filename, '.'), 1) ?: $filename;

    $this->convertTo($outputMode);

    return response($this->getOutput())->withHeaders(
      $this->responseHeaders($filename, $outputMode, $inline)
    );
  }

  public function download(string $filename): Response
  {
    return $this->makeFileResponse($filename, false);
  }

  public function inline(string $filename): Response
  {
    return $this->makeFileResponse($filename, true);
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

    $this->inputPath = $this->isUrl
      ? $this->source
      : $this->tempFilename();

    $this->outputPath = $this->tempFilename();

    if (!$this->isUrl) {
      $this->writeTempInputFile();
    }
  }

  private function pushToCommand(string $key, $value): void
  {
    if (is_bool($value) && $value) {
      array_push($this->command, $key);
    } else if ($value) {
      array_push($this->command, $key, $value);
    }
  }

  private function compileCommand(): void
  {
    $this->command = [
      $this->processBinary,
      $this->inputPath,
      $this->outputPath,
      '--quiet',
      '--format', $this->outputMode,
      '--encoding', $this->outputEncoding,
    ];

    $this->pushToCommand('--presentational-hints', $this->presentationalHints);
    $this->pushToCommand('--base-url', $this->baseUrl);
    $this->pushToCommand('--media-type', $this->mediaType);

    if ($this->outputMode === 'png') {
      $this->pushToCommand('--resolution', $this->resolution);
    }

    foreach ($this->attachments as $attachment) {
      $this->pushToCommand('--attachment', $attachment);
    }

    foreach ($this->stylesheets as $stylesheet) {
      $this->pushToCommand('--stylesheet', $stylesheet);
    }
  }

  private function process(): void
  {
    $this->compileCommand();

    $process = new Process($this->command, null, ['LC_ALL' => 'en_US.UTF-8']);
    $process->setTimeout($this->processTimeout)->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    if (!$this->isUrl) {
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
