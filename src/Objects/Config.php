<?php

declare(strict_types=1);

namespace WeasyPrint\Objects;

use Illuminate\Contracts\Support\Arrayable;
use WeasyPrint\Enums\PDFVariant;
use WeasyPrint\Enums\PDFVersion;
use WeasyPrint\Exceptions\InvalidConfigValueException;

final class Config implements Arrayable
{
  /**
   * @param string|null $binary The path to the WeasyPrint binary on
   * your system. If it is available on your system globally, the
   * package will find and use it. If not, then you will need to
   * specify the absolute path.
   *
   * @param string $cachePrefix The cache prefix to use for the
   * temporary filename.
   *
   * @param int $timeout The amount of seconds to allow a conversion
   * to run for.
   *
   * @param string $inputEncoding Force the input character encoding.
   * utf-8 is recommended.
   *
   * @param bool $presentationalHints Enable or disable HTML
   * Presentational Hints.
   *
   * @param string|null $mediaType Optionally set the media type to
   * use for CSS [at]media. Defaults to `print` at binary-level.
   *
   * @param string|null $baseUrl Optionally set the base URL for
   * relative URLs in the HTML input.
   *
   * @param array $stylesheets Stylesheets to use alongside the
   * HTML input. Each stylesheet may the absolute path to a
   * file, or a URL.
   *
   * **NOTE:** It is recommended to set this at runtime
   * using the `addStylesheet` method.
   *
   * @param array $processEnvironment The environment variables
   * passed to Symfony Process when executing the WeasyPrint binary.
   *
   * @param PDFVariant|string|null $pdfVariant Optionally specify a
   * PDF variant. See the PDFVariant enum cases.
   *
   * @param PDFVersion|string|null $pdfVersion Optionally specify a
   * PDF version. See the PDFVersion enum cases.
   *
   * @param bool $skipCompression For debugging purposes, do not
   * compress PDFs.
   *
   * @param bool $customMetadata Include custom HTML meta tags
   * in PDF metadata.
   *
   * @param bool $srgb Include sRGB color profile.
   *
   * @param bool $optimizeImages Optimize the size of embedded images
   * with no quality loss.
   *
   * @param bool $fullFonts When possible, embed unmodified font
   * files in the PDF.
   *
   * @param bool $hinting Keep hinting information in embedded font files.
   *
   * @param int|null $dpi Set the maximum resolution of images
   * embedded in the PDF.
   *
   * @param int|null $jpegQuality Set the JPEG output quality,
   * from 0 (worst) to 95 (best).
   *
   * @param bool $pdfForms Render PDF forms from HTML elements.
   */
  public function __construct(
    public string|null $binary = null,
    public string $cachePrefix = 'weasyprint_cache',
    public int $timeout = 60,
    public string $inputEncoding = 'utf-8',
    public bool $presentationalHints = true,
    public string|null $mediaType = null,
    public string|null $baseUrl = null,
    public array $stylesheets = [],
    public array $processEnvironment = ['LC_ALL' => 'en_US.UTF-8'],
    public PDFVariant|string|null $pdfVariant = null,
    public PDFVersion|string|null $pdfVersion = null,
    public bool $skipCompression = false,
    public bool $customMetadata = false,
    public bool $srgb = false,
    public bool $optimizeImages = false,
    public bool $fullFonts = false,
    public bool $hinting = false,
    public int|null $dpi = null,
    public int|null $jpegQuality = null,
    public bool $pdfForms = false,
  ) {
    $this->runAssertions();
    $this->expandEnums();
  }

  public function runAssertions(): void
  {
    if ($this->dpi && $this->dpi < 0) {
      throw new InvalidConfigValueException(
        key: 'dpi',
        value: (string) $this->dpi,
        expected: '>0',
      );
    }

    if ($this->jpegQuality && ($this->jpegQuality < 0 || $this->jpegQuality > 95)) {
      throw new InvalidConfigValueException(
        key: 'jpegQuality',
        value: (string) $this->jpegQuality,
        expected: '0..95',
      );
    }

    $validEncodings = array_map('strtolower', mb_list_encodings());

    if (!in_array($this->inputEncoding, $validEncodings, true)) {
      throw new InvalidConfigValueException(
        key: 'inputEncoding',
        value: $this->inputEncoding,
        expected: collect($validEncodings)->join(', '),
      );
    }
  }

  public function toArray(): array
  {
    return [
      'binary' => $this->binary,
      'cachePrefix' => $this->cachePrefix,
      'timeout' => $this->timeout,
      'inputEncoding' => $this->inputEncoding,
      'presentationalHints' => $this->presentationalHints,
      'mediaType' => $this->mediaType,
      'baseUrl' => $this->baseUrl,
      'stylesheets' => $this->stylesheets,
      'processEnvironment' => $this->processEnvironment,
      'pdfVariant' => $this->pdfVariant?->value,
      'pdfVersion' => $this->pdfVersion?->value,
      'skipCompression' => $this->skipCompression,
      'customMetadata' => $this->customMetadata,
      'srgb' => $this->srgb,
      'optimizeImages' => $this->optimizeImages,
      'fullFonts' => $this->fullFonts,
      'hinting' => $this->hinting,
      'dpi' => $this->dpi,
      'jpegQuality' => $this->jpegQuality,
      'pdfForms' => $this->pdfForms,
    ];
  }

  private function expandEnums(): void
  {
    if (is_string($this->pdfVariant)) {
      $this->pdfVariant = PDFVariant::tryFrom($this->pdfVariant);
    }

    if (is_string($this->pdfVersion)) {
      $this->pdfVersion = PDFVersion::tryFrom($this->pdfVersion);
    }
  }
}
