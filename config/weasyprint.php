<?php

return [

  /**
   * The absolute path to the WeasyPrint binary on your system.
   * @param string
   */
  'binary' => '/usr/local/bin/weasyprint',

  /**
   * The cache prefix to use for the temporary filename.
   * @param string
   */
  'cachePrefix' => 'weasyprint_cache',

  /**
   * The amount of seconds to allow a conversion to run for.
   * @param int
   */
  'timeout' => 3600,

  /**
   * Force the input character encoding. utf-8 is recommended.
   * @param string
   */
  'inputEncoding' => 'utf-8',

  /**
   * Enable or disable HTML Presentational Hints.
   * When enabled, `--presentational-hints` is passed to the binary.
   * @param bool
   */
  'presentationalHints' => true,

  /**
   * Optionally enable image optimization, where WeasyPrint will attempt
   * to reduce the size of embedded images.
   * When enabled, `--optimize-images` is passed to the binary.
   * Note: this feature requires WeasyPrint 52 or greater.
   * @param bool
   */
  'optimizeImages' => false,

  /**
   * Optionally set the output resolution in pixels per inch.
   * For PNG output only. Defaults to 96 (which means that PNG pixels match CSS pixels) at binary-level.
   * @param int|null
   */
  'resolution' => null,

  /**
   * Optionally set the media type to use for CSS @media.
   * Defaults to `print` at binary-level.
   * @param string|null
   */
  'mediaType' => null,

  /**
   * Optionally set the base URL for relative URLs in the HTML input.
   * Defaults to the input’s own URL at binary-level.
   * @param string|null
   */
  'baseUrl' => null,

  /**
   * Optionally provide an array of stylesheets to use alongside the HTML input.
   * Each stylesheet may the absolute path to a file, or a URL.
   * @param string[]|null
   */
  'stylesheets' => null,

];
