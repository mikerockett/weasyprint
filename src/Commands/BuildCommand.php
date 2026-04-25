<?php

declare(strict_types=1);

namespace WeasyPrint\Commands;

use WeasyPrint\Exceptions\AttachmentNotFoundException;
use WeasyPrint\Exceptions\BinaryNotFoundException;
use WeasyPrint\Objects\Attachment;
use WeasyPrint\Objects\Config;

final class BuildCommand extends BaseCommand
{
  use FindsBinary;

  public function __construct(
    Config $config,
    string $inputPath,
    string $outputPath,
    protected array $attachments = [],
    protected array $xmpMetadata = [],
  ) {
    $this->config = $config;

    $this->arguments = collect([
      $this->resolveBinary(),
      $inputPath,
      $outputPath,
    ]);

    $this->prepareArguments();
  }

  private function resolveBinary(): string
  {
    if ($this->config->binary !== null) {
      if (!is_executable($this->config->binary)) {
        throw new BinaryNotFoundException($this->config->binary);
      }

      return $this->config->binary;
    }

    return $this->findBinary();
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
      'no-http-redirects' => $this->config->noHttpRedirects,
      'fail-on-http-errors' => $this->config->failOnHttpErrors,
    ]);

    $arguments->each(
      fn(mixed $value, string $name) => $this->maybePushArgument($name, $value),
    );

    collect($this->attachments)->each(
      function (Attachment $attachment): void {
        if (!is_file($attachment->path)) {
          throw new AttachmentNotFoundException($attachment->path);
        }

        $this->maybePushArgument('attachment', $attachment->path);
        $this->maybePushArgument('attachment-relationship', $attachment->relationship);
      },
    );

    collect($this->xmpMetadata)->each(
      fn(string $path) => $this->maybePushArgument('xmp-metadata', $path),
    );

    collect($this->config->stylesheets)->each(
      fn(string $path) => $this->maybePushArgument('stylesheet', $path),
    );
  }
}
