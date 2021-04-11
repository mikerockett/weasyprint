<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Support\Collection;
use Symfony\Component\Process\{Exception\ProcessFailedException, Process};
use WeasyPrint\Enums\OutputType;
use WeasyPrint\Exceptions\AttachmentNotFoundException;
use WeasyPrint\Objects\Config;

class Command
{
  protected Collection $arguments;

  public function __construct(
    protected Config $config,
    protected OutputType $outputType,
    string $inputPath,
    string $outputPath,
    protected array $attachments = []
  ) {
    $this->arguments = new Collection([
      $config->getBinary(),
      $inputPath,
      $outputPath,
      '--quiet',
      '--format', $outputType->getValue(),
      '--encoding', $config->getInputEncoding(),
    ]);

    $this->prepareOptionalArguments();
  }

  protected function maybePushArgument(string $key, $value): void
  {
    if ($value === true) {
      $this->arguments->push($key);
    } else if ($value) {
      $this->arguments->push($key, $value);
    }
  }

  protected function prepareOptionalArguments(): void
  {
    $this->maybePushArgument(
      '--presentational-hints',
      $this->config->usePresentationalHints()
    );

    $this->maybePushArgument(
      '--base-url',
      $this->config->getBaseUrl()
    );

    $this->maybePushArgument(
      '--media-type',
      $this->config->getMediaType()
    );

    if ($this->outputType->is(OutputType::png())) {
      $this->maybePushArgument(
        '--resolution',
        $this->config->getResolution()
      );
    }

    if ($this->outputType->is(OutputType::pdf())) {
      foreach ($this->attachments as $attachment) {
        if (!is_file($attachment)) {
          throw new AttachmentNotFoundException($attachment);
        }

        $this->maybePushArgument('--attachment', $attachment);
      }

      if ($stylesheets = $this->config->getStylesheets()) {
        foreach ($stylesheets as $stylesheet) {
          $this->maybePushArgument('--stylesheet', $stylesheet);
        }
      }
    }
  }

  public function execute()
  {
    $process = new Process(
      command: $this->arguments->toArray(),
      env: ['LC_ALL' => 'en_US.UTF-8'],
    );

    $process->setTimeout($this->config->getTimeout())->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }
  }
}
