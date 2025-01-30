<?php

return (array) new \WeasyPrint\Objects\Config(
  binary: env('WEASYPRINT_BINARY'),
  cachePrefix: env('WEASYPRINT_CACHE_PREFIX', 'weasyprint_cache'),
  timeout: (int) env('WEASYPRINT_TIMEOUT', '60'),
  inputEncoding: env('WEASYPRINT_INPUT_ENCODING', 'utf-8'),
  presentationalHints: env('WEASYPRINT_PRESENTATIONAL_HINTS', true),
  mediaType: env('WEASYPRINT_MEDIA_TYPE'),
  baseUrl: env('WEASYPRINT_BASE_URL'),
  stylesheets: [],
  processEnvironment: [
    'LC_ALL' => env('WEASYPRINT_LOCALE', 'en_US.UTF-8'),
  ],
  pdfVariant: \WeasyPrint\Enums\PDFVariant::fromEnvironment(
    'WEASYPRINT_PDF_VARIANT',
  ),
  pdfVersion: \WeasyPrint\Enums\PDFVersion::fromEnvironment(
    'WEASYPRINT_PDF_VERSION',
  ),
  skipCompression: env('WEASYPRINT_SKIP_COMPRESSION', false),
  customMetadata: env('WEASYPRINT_CUSTOM_METADATA', false),
  srgb: env('WEASYPRINT_SRGB', false),
  optimizeImages: env('WEASYPRINT_OPTIMIZE_IMAGES', false),
  fullFonts: env('WEASYPRINT_FULL_FONTS', false),
  hinting: env('WEASYPRINT_HINTING', false),
  dpi: env('WEASYPRINT_DPI', null),
  jpegQuality: env('WEASYPRINT_JPEG_QUALITY', null),
  pdfForms: env('WEASYPRINT_PDF_FORMS', false),
);
