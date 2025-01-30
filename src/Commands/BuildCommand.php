<?php

declare(strict_types=1);

namespace WeasyPrint\Commands;

use WeasyPrint\Exceptions\AttachmentNotFoundException;
use WeasyPrint\Objects\Config;

final class BuildCommand extends BaseCommand
{
  use FindsBinary;

  public function __construct(
    Config $config,
    string $inputPath,
    string $outputPath,
    protected array $attachments = [],
  ) {
    $this->config = $config;

    $this->arguments = collect([
      $config->binary ?? $this->findBinary(),
      $inputPath,
      $outputPath,
    ]);

    $this->prepareArguments();
  }

  private function prepareArguments(): void
  {
    $arguments = collect([
      'quiet' => true,
      'encoding' => $this->config->inputEncoding,
      'presentational-hints' => $this->config->presentationalHints,
      'base-url' => $this->config->baseUrl,
      'media-type' => $this->config->mediaType,
      'pdf-variant' => $this->config->pdfVariant?->value,
      'pdf-version' => $this->config->pdfVersion?->value,
      'uncompressed-pdf' => $this->config->skipCompression,
      'custom-metadata' => $this->config->customMetadata,
      'srgb' => $this->config->srgb,
      'optimize-images' => $this->config->optimizeImages,
      'full-fonts' => $this->config->fullFonts,
      'hinting' => $this->config->hinting,
      'dpi' => $this->config->dpi,
      'jpeg-quality' => $this->config->jpegQuality,
      'pdf-forms' => $this->config->pdfForms,
    ]);

    $arguments->each(
      fn(mixed $value, string $name) => $this->maybePushArgument($name, $value),
    );

    collect($this->attachments)->each(
      function (string $path): void {
        if (!is_file($path)) {
          throw new AttachmentNotFoundException($path);
        }

        $this->maybePushArgument('attachment', $path);
      },
    );

    collect($this->config->stylesheets)->each(
      fn(string $path) => $this->maybePushArgument('stylesheet', $path),
    );
  }
}
