# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [11.0.1] - 2026-04-30

### Changed

- Argument injection resistance: `--` separator before positional args, array-form `Process` instead of `fromShellCommandline`.
- `MediaType` enum replacing freeform string input, with validation on construction.
- `cachePrefix` validation (rejects path separators and null bytes).
- Temp file path confinement to `sys_get_temp_dir()`, with `chmod 0o600` and proper error handling on `tempnam` failure.
- `WeasyPrintException` marker interface on all exception types.
- `FakeWeasyPrint::getSource()` now throws `SourceNotSetException` instead of returning `null`.
- Added Mago for linting and static analysis (baselines for incremental adoption).
- CI: `--prefer-lowest` matrix entry, Mago lint/analyze steps.

## [11.0.0] - 2026-04-25

Version 11 refactors the package to be framework-agnostic. It has been renamed to **WeasyPrint for PHP** and Laravel-specific features are now organised under the `Integration\Laravel` namespace. While it maintains Laravel compatibility, core functionality can now be used independently of any framework.

Please refer to the [UPGRADING.md](UPGRADING.md) guide for detailed migration instructions.

### Added

- PDF Variants: PDF/A-1a, PDF/A-2a, PDF/A-3a, PDF/A-4e, PDF/A-4f, PDF/UA-2, PDF/X-1a, PDF/X-3, PDF/X-4, PDF/X-5g, and debug.
- `Output` is now `Stringable` (useful to pass to `Storage::put`).
- Tests are now split: one suite for the core package, another for Laravel integration testing.

### Changed

- Core classes have been renamed, and Laravel-specific classes have been moved into `Integration\Laravel`.
- Default config file has been moved into `Integration\Laravel`.
- Streamed responses now use Symfony's `StreamedResponse` directly instead of via Laravel's `ResponseFactory::streamDownload`.
- Internal pipeline now uses the latest package version, and is assembled with a `PipelineBuilder`.
- Formatting is now done with PHP CS Fixer directly, using `@PER-CS2x0` with some extra rules for strictness (including strict-types).
- Requires WeasyPrint 67.
- Requires PHP >= 8.3.
- Requires Laravel >= 12.

### Removed

- `Service::instance`.
- `Output::putFile`.

---

The entries below are from the previous **WeasyPrint for Laravel** package.

## [10.7.0] - 2026-03-24

### Added

- Support for Laravel 13 and Symfony 8.

## [10.6.0] - 2026-01-20

### Changed

- Accepts WeasyPrint 68 (version constraint only; full support in 11.x).

## [10.5.0] - 2025-12-12

### Changed

- Accepts WeasyPrint 67 (version constraint only; full support in 11.x).

## [10.4.0] - 2025-08-19

### Added

- Support for WeasyPrint 66 and Rockett\Pipeline 4.0.

## [10.3.0] - 2025-04-09

### Fixed

- Base `stream()` method signatures corrected to make `StreamMode::INLINE` the default `$mode` as it is declared after `$headers = []` (see #13).

## [10.2.0] - 2025-04-04

### Added

- Support for WeasyPrint 65.

## [10.1.0] - 2025-02-25

### Added

- Support for Laravel 12.

## [10.0.1] - 2025-01-30

### Fixed

- `srgb` config env default.

## [10.0.0] - 2025-01-30

### Added

- Support for the `srgb` and `custom-metadata` boolean flags via Config (both default to `false`).

### Changed

- Config property descriptions in the default config file have been moved into the Config class.
- Support for WeasyPrint 63.x and 64.x; drops support for older versions.
- The PDF/UA-1 variant deprecation has been reverted.

---

<details>
<summary>Unsupported versions</summary>

## [9.0.0] - 2024-05-01

### Added

- Support for [class-based sources](https://weasyprint.rockett.pw/v10/class-instantiation.html).
- Support for the PDF/A-2u, PDF/A-3u and PDF/A-4u variants.
- `StreamMode` enum to dynamically distinguish between `download()` and `inline()`. A `stream()` helper method is also available. ([docs](https://weasyprint.rockett.pw/v10/output.html#stream-download-and-inline))

### Changed

- `Source::$source` is now private.
- Drops support for WeasyPrint < 61.0.
- Drops support for PHP < 8.2.

### Deprecated

- The PDF/UA-1 variant is marked as deprecated as WeasyPrint's source does not account for it.

## [8.1.0] - 2024-03-13

### Added

- Support for Laravel 11 and WeasyPrint 61.

Note: WeasyPrint 61.0 and 61.1 are not supported due to a [security issue](https://github.com/Kozea/WeasyPrint/releases/tag/v61.2). Version 8 of the package is the last to support PHP 8.1 and versions of WeasyPrint < 61.

## [8.0.0] - 2023-10-17

This release drops support for WeasyPrint < v59. Compatibility is now based solely on CLI flags available in each WeasyPrint version.

### Added

- `skipCompression`, `optimizeImages`, `fullFonts`, `hinting`, `dpi`, `jpegQuality`, `pdfForms` config options.
- Config option validation for `dpi`, `jpegQuality`, `mediaType`, and `inputEncoding`.

### Changed

- Service instances may now only be resolved via the Service Container (`Service::new()` removed in favour of `Service::instance()`).
- Default config is now class-based for type-safety.
- Runtime config may now only be tapped (`tapConfig`) or overridden (`setConfig`).
- Default `timeout` is now 60 seconds.
- All tests moved to Pest 2.

### Removed

- `optimizeSize` config option.

## [7.1.0] - 2023-10-11

### Added

- Support for WeasyPrint 58.
- `pdfVersion` and `pdfVariant` configuration properties (requires WeasyPrint >= 58).

## [7.0.0] - 2023-02-17

### Changed

- Adds support for Laravel 10, drops support for Laravel 8.
- Minimum PHP version is now 8.1.

## [6.1.0] - 2022-02-20

### Added

- Support for Laravel 9 and WeasyPrint v54.

## [6.0.0] - 2021-08-01

This version targets WeasyPrint v53, which drops PNG support due to its new rendering engine. Requires Laravel 8.47+ for [scoped singletons](https://laravel.com/docs/8.x/container#binding-scoped).

### Added

- Configuration file now supports environment variables.

### Changed

- The service class is no longer immutable (scoped singleton).
- Internally uses a [pipeline](https://github.com/mikerockett/pipeline) to prepare and call the WeasyPrint binary.
- `binary` config no longer has a default path; the package will attempt to locate the binary in `PATH`.

### Removed

- `to()`, `toPdf()` and `toPng()` methods.
- `OutputType` enumeration class.
- `optimizeImages` config option (replaced by `optimizeSize`).
- `resolution` config option (no PNG support).

## [5.0.0] - 2021-04-11

### Added

- Full support for the [Laravel Service Container](https://laravel.com/docs/container) with a service-based architecture.
- [Laravel Octane](https://github.com/laravel/octane) compatibility.
- Config-based setup replacing fluent helpers.
- `--optimize-images` flag via `optimizeImages` config option (requires WeasyPrint >= 52).
- `putFile` method on `Output` for saving via Laravel's [Filesystem](https://laravel.com/docs/filesystem).

### Changed

- `download` and `inline` return `Symfony\Component\HttpFoundation\StreamedResponse` instead of `Illuminate\Http\Response`.
- `download` and `inline` may be called on the service or on the output from `build()`.

### Removed

- Static `make` method (use `prepareSource($source)->build()` instead).
- `view` method (pass a `Renderable` to `prepareSource` instead).
- `set*` and `add*` configuration helpers (except `addAttachment`).

## [4.0.0] - 2020-09-09

### Changed

- Drops support for PHP < 7.3 and Laravel < 7.0.
- Upgrades `orchestra/testbench` to v6.

## [3.0.0] - 2020-03-21

### Added

- Support for `symfony/process` v5.

## [2.0.1] - 2020-03-21

### Added

- Support for Laravel 7.

## [2.0.0] - 2020-02-13

### Added

- `addAttachment`, `setResolution`, `setMediaType`, `setPresentationalHints`, `setOutputEncoding` fluent helpers.
- `InvalidOutputModeException` when the output mode is invalid.

### Removed

- `convert` method (use `toPdf`, `toPng`, `download` or `inline` instead).

## [1.0.5] - 2020-02-09

### Added

- Support for URLs.
- `toPdf` and `toPng` shorthand helpers.

### Changed

- Does not re-convert if the output is already available.

## [1.0.4] - 2019-09-17

### Added

- Stylesheet support with `addStylesheet`.

## [1.0.3] - 2019-09-17

### Added

- Base URL support with `setBaseUrl`.
- `download` and `inline` helpers.

## [1.0.2] - 2019-09-17

### Fixed

- `view` method is now correctly static.

## [1.0.0] - 2019-09-16

Initial release.

</details>
