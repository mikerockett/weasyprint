<?php

return [

  /**
   * The path to the WeasyPrint binary on your system.
   * If it is available on your system globally, the package will find and use it.
   * If not, then you will need to specify the absolute path.
   *
   * @param string
   */
  'binary' => env('WEASYPRINT_BINARY'),

  /**
   * The environment variables passed to Symfony Process when
   * executing the WeasyPrint binary.
   *
   * @param array
   */
  'processEnvironment' => ['LC_ALL' => env('WEASYPRINT_LOCALE', 'en_US.UTF-8')],

  /**
   * The cache prefix to use for the temporary filename.
   *
   * @param string
   */
  'cachePrefix' => env('WEASYPRINT_CACHE_PREFIX', 'weasyprint_cache'),

  /**
   * The amount of seconds to allow a conversion to run for.
   *
   * @param int
   */
  'timeout' => env('WEASYPRINT_TIMEOUT', 120),

  /**
   * Force the input character encoding. utf-8 is recommended.
   *
   * @param string
   */
  'inputEncoding' => env('WEASYPRINT_INPUT_ENCODING', 'utf-8'),

  /**
   * Enable or disable HTML Presentational Hints.
   * When enabled, `--presentational-hints` is passed to the binary.
   *
   * @param bool
   */
  'presentationalHints' => env('WEASYPRINT_PRESENTATIONAL_HINTS', true),

  /**
   * Optionally set the media type to use for CSS @media.
   * Defaults to `print` at binary-level.
   *
   * @param string|null
   */
  'mediaType' => env('WEASYPRINT_MEDIA_TYPE'),

  /**
   * Optionally set the base URL for relative URLs in the HTML input.
   * Defaults to the input’s own URL at binary-level.
   *
   * @param string|null
   */
  'baseUrl' => env('WEASYPRINT_BASE_URL'),

  /**
   * Optionally provide an array of stylesheets to use alongside the HTML input.
   * Each stylesheet may the absolute path to a file, or a URL.
   * It is recommended to do this at runtime.
   *
   * @param string[]|null
   */
  'stylesheets' => null,

  /**
   * Optionally specify a PDF variant.
   * Use one of: pdf/a-1b, pdf/a-2b, pdf/a-3b, pdf/a-4b, or pdf/ua-1
   * Or:         case direct from PDFVariant
   *
   * @param WeasyPrint\Enums\PDFVariant
   */
  'pdfVariant' => WeasyPrint\Enums\PDFVariant::fromEnvironment('WEASYPRINT_PDF_VARIANT'),

  /**
   * Optionally specify a PDF version.
   *
   * @param string
   */
  'pdfVersion' => WeasyPrint\Enums\PDFVersion::fromEnvironment('WEASYPRINT_PDF_VERSION'),

];
