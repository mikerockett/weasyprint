<?php

declare(strict_types=1);

namespace WeasyPrint\Commands;

use Illuminate\Support\Collection;
use WeasyPrint\Exceptions\AttachmentNotFoundException;
use WeasyPrint\Objects\Config;

class BuildCommand extends BaseCommand
{
  use FindsBinary;

  public function __construct(
    Config $config,
    string $inputPath,
    string $outputPath,
    protected array $attachments = []
  ) {
    $this->config = $config;

    $this->arguments = new Collection([
      $config->getBinary() ?? $this->findBinary(),
      $inputPath,
      $outputPath,
      '--quiet',
      '--encoding', $config->getInputEncoding(),
    ]);

    $this->prepareOptionalArguments();
  }

  private function prepareOptionalArguments(): void
  {
    $this->maybePushArgument(
      'presentational-hints',
      $this->config->usePresentationalHints()
    );

    $this->maybePushArgument(
      'base-url',
      $this->config->getBaseUrl()
    );

    $this->maybePushArgument(
      'media-type',
      $this->config->getMediaType()
    );

    $this->maybePushArgument(
      'pdf-variant',
      $this->config->getPdfVariant()?->value
    );

    $this->maybePushArgument(
      'pdf-version',
      $this->config->getPdfVersion()?->value
    );

    foreach ($this->attachments as $attachment) {
      if (!is_file($attachment)) {
        throw new AttachmentNotFoundException($attachment);
      }

      $this->maybePushArgument('attachment', $attachment);
    }

    if ($stylesheets = $this->config->getStylesheets()) {
      foreach ($stylesheets as $stylesheet) {
        $this->maybePushArgument('stylesheet', $stylesheet);
      }
    }
  }
}
