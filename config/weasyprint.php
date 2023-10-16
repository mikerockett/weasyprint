<?php

return (array) new \WeasyPrint\Objects\Config(
  /**
   * The path to the WeasyPrint binary on your system. If it is available on
   * your system globally, the package will find and use it. If not, then
   * you will need to specify the absolute path.
   */
  binary: env('WEASYPRINT_BINARY'),

  /**
   * The cache prefix to use for the temporary filename.
   */
  cachePrefix: env('WEASYPRINT_CACHE_PREFIX', 'weasyprint_cache'),

  /**
   * The amount of seconds to allow a conversion to run for.
   */
  timeout: (int) env('WEASYPRINT_TIMEOUT', '60'),

  /**
   * Force the input character encoding. utf-8 is recommended.
   */
  inputEncoding: env('WEASYPRINT_INPUT_ENCODING', 'utf-8'),

  /**
   * Enable or disable HTML Presentational Hints.
   */
  presentationalHints: env('WEASYPRINT_PRESENTATIONAL_HINTS', true),

  /**
   * Optionally set the media type to use for CSS @media.
   * Defaults to `print` at binary-level.
   */
  mediaType: env('WEASYPRINT_MEDIA_TYPE'),

  /**
   * Optionally set the base URL for relative URLs in the HTML input.
   */
  baseUrl: env('WEASYPRINT_BASE_URL'),

  /**
   * Stylesheets to use alongside the HTML input.
   * Each stylesheet may the absolute path to a file, or a URL.
   * NOTE: It is recommended to do this at runtime.
   */
  stylesheets: [],

  /**
   * The environment variables passed to Symfony Process when executing
   * the WeasyPrint binary.
   */
  processEnvironment: ['LC_ALL' => env('WEASYPRINT_LOCALE', 'en_US.UTF-8')],

  /**
   * Optionally specify a PDF variant.
   */
  pdfVariant: WeasyPrint\Enums\PDFVariant::fromEnvironment('WEASYPRINT_PDF_VARIANT'),

  /**
   * Optionally specify a PDF version.
   */
  pdfVersion: WeasyPrint\Enums\PDFVersion::fromEnvironment('WEASYPRINT_PDF_VERSION'),

  /**
   * For debugging purposes, do not compress PDFs.
   */
  skipCompression: env('WEASYPRINT_SKIP_COMPRESSION', false),

  /**
   * Optimize the size of embedded images with no quality loss.
   */
  optimizeImages: env('WEASYPRINT_OPTIMIZE_IMAGES', false),

  /**
   * When possible, embed unmodified font files in the PDF.
   */
  fullFonts: env('WEASYPRINT_FULL_FONTS', false),

  /**
   * Keep hinting information in embedded font files.
   */
  hinting: env('WEASYPRINT_HINTING', false),

  /**
   * Set the maximum resolution of images embedded in the PDF.
   */
  dpi: env('WEASYPRINT_DPI', null),

  /**
   * Set the JPEG output quality, from 0 (worst) to 95 (best).
   */
  jpegQuality: env('WEASYPRINT_JPEG_QUALITY', null),

  /**
   * Render PDF forms from HTML elements.
   */
  pdfForms: env('WEASYPRINT_PDF_FORMS', false),
);
