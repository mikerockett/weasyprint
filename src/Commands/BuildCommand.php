<?php

declare(strict_types=1);

namespace WeasyPrint\Commands;

use Illuminate\Support\Collection;
use WeasyPrint\Exceptions\AttachmentNotFoundException;
use WeasyPrint\Objects\Config;

final class BuildCommand extends BaseCommand
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
      $config->binary ?? $this->findBinary(),
      $inputPath,
      $outputPath,
      '--quiet',
      '--encoding', $config->inputEncoding,
    ]);

    $this->prepareOptionalArguments();
  }

  private function prepareOptionalArguments(): void
  {
    $arguments = collect([
      'presentational-hints' => $this->config->presentationalHints,
      'base-url' => $this->config->baseUrl,
      'media-type' => $this->config->mediaType,
      'pdf-variant' => $this->config->pdfVariant?->value,
      'pdf-version' => $this->config->pdfVersion?->value,
    ]);

    $arguments->each(
      fn (mixed $value, string $name) => $this->maybePushArgument($name, $value)
    );

    collect($this->attachments)->each(
      function (string $path): void {
        if (!is_file($path)) {
          throw new AttachmentNotFoundException($path);
        }

        $this->maybePushArgument('attachment', $path);
      }
    );

    collect($this->config->stylesheets)->each(
      fn (string $path) => $this->maybePushArgument('stylesheet', $path)
    );
  }
}
