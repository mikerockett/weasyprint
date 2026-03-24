# WeasyPrint for Laravel — Changelog

## 10.x (Major Release) `Current`

#### Features and Enhancements

- Adds support for the `srgb` and `custom-metadata` boolean flags via Config (both default to `false`).

#### Changes

- Documentation: Config property descriptions in the default config file have been moved into the Config class.
- Versioning: Adds support for WeasyPrint **63.x** and **64.x** and drops support for older versions.

#### Deprecations

- The PDF/UA-1 variant deprecation has been reverted.

### Minor Releases

- `10.1.0` - Versioning: Adds support for Laravel 12.
- `10.2.0` - Versioning: Adds support for WeasyPrint 65.
- `10.3.0` - Corrects base `stream()` method signatures to make `StreamMode::INLINE` the default `$mode` as it is declared after `$headers = []` (see #13)
- `10.4.0` - Versioning: Adds support for WeasyPrint 66 and Rockett\Pipeline 4.0.

### Patch Releases

- `10.0.1` - fix srgb config env default [37baef00]

---

## 9.x (Major Release) `Maintenance`

#### Features and Enhancements

- Adds support for [class-based sources](https://weasyprint.rockett.pw/class-instantiation.html).
- Adds support for the **PDF/A-2u**, **PDF/A-3u** and **PDF/A-4u** variants.
- Introduces a `StreamMode` enum to dynamically distinguish between `download()` and `inline()`. Alongside this, a `stream()` helper method is available, should you not want to use the `download()` or `inline()` helpers directly (these use `stream()` under the hood). ([docs](https://weasyprint.rockett.pw/output.html#stream-download-and-inline))

#### Changes

- Internal: `Source::$source` is now private.
- Versioning: Drops support for WeasyPrint < 61.0.
- Versioning: Drops support for PHP < 8.2.

#### Deprecations

- The PDF/UA-1 variant is marked as deprecated as WeasyPrint’s source does not account for it.

<hr />

<details>
<summary>Unsupported Versions</summary>

## 8.1.0 (Minor Release) `Unsupported`

This release adds support for Laravel 11 and WeasyPrint 61. Versions 61.0 and 61.1 are not supported due to a security issue noted [here](https://github.com/Kozea/WeasyPrint/releases/tag/v61.2).

Version 8 of the package will be the last to support PHP 8.1 and versions of WeasyPrint < 61.

## 8.0.0 (Breaking Release) `Unsupported`

This release drops support for WeasyPrint < v59. If you are constrained to an older version, an older version of the package that supports that version will be required.

Going forward, compatibility of this package against a particular WeasyPrint version will be based solely on CLI flags available in that version.

- If a CLI property is removed in a WeasyPrint release, then it will become unsupported in a new version of the package.
- If one is added, then it will be unsupported until a new version of the package is released.

In general, only the latest major version of WeasyPrint and, by extension, the package will be supported at any given time. However, where CLI flags do not change between versions, and WeasyPrint only updates internal features in relation to rendering PDFs, then all such versions will continue to be supported. An example of this is WeasyPrint 59.0 → 60.0, where CLI flags did not change.

If you are using an unsupported version of WeasyPrint, attempts to build a PDF will fail with an exception.

### Changes:

- Service instances may now only be resolved via the Service Container (`Service::new()` has been removed in favour of `Service::instance()`).
- The default config is now class-based to introduce some type-safety.
- Runtime config may now only be tapped (using `tapConfig`) or overridden (using `setConfig`).
- The default `timeout` is now 60 seconds.
- The `optimizeSize` config option has been removed.
- The `skipCompression`, `optimizeImages`, `fullFonts`, `hinting`, `dpi`, `jpegQuality`, `pdfForms` config options have been added.
- Some config options are now validated, including the new `dpi` and `jpegQuality` options, as well as existing `mediaType` and `inputEncoding` options. An exception will be thrown if these options are invalid.
- All tests have been moved to Pest 2. Coverage removed for the time being.

## 7.1.0 `Unsupported`

This release adds support for WeasyPrint 58, along with two new configuration properties, `pdfVersion` and `pdfVariant`, which may only be used in versions 58 and greater. Custom meta-data has not been added in this release.

> Note: Support for WeasyPrint 59 and 60 to come in the next major package release, which will drop support for older versions of WeasyPrint.

## 7.0.0 `Unsupported`

This release adds support for Laravel 10 and drops support for Laravel 8. The minimum-required version of PHP is now 8.1. As there have been no significant API changes to WeasyPrint, this package continues to support v53+.

## 6.1.0 `Unsupported`

This release adds support for Laravel 9, and works just fine with WeasyPrint v54.

## 6.0.0 (Breaking Release) `Unsupported`

This version is specifically designed around WeasyPrint v53, which drops support for PNGs due to its new rendering engine. Overall, this simplifies things from an interface perspective – you only need to prepare the source, build the `Output`, and do what you need with it.

Over and above the changes noted below, the package now requires Laravel 8.47+, which adds support for [scoped singletons](https://laravel.com/docs/8.x/container#binding-scoped). In the previous version (v5) of this package, the singleton was immutable, which meant that every mutable-by-design method would actually return a cloned instance of the service.

### What’s New

- The configuration file now supports environment variables, which generally removes the need to publish it. See the [readme](readme.md#available-configuration-options) for a list of available options.

### Breaking Changes

- The `to()`, `toPdf()` and `toPng()` methods have been removed.
- Likewise, the `OutputType` enumeration class has been removed. Under the hood, the `--format` flag has been removed.
- The `optimizeImages` config option has been removed in favor of `optimizeSize`.
- The `resolution` config option has been removed, due to lack of PNG support.

### Other Changes

- The `binary` config option previously declared a sensible default of `/usr/local/bin/weasyprint`. However, this may not always be the case as WeasyPrint may be installed in a virtual environment, which does not conform to that path. Additionally, some Linux distros place the binary elsewhere on the system. With the removal of this default, the package will attempt to locate the binary, which means it needs to be in your `PATH`. If it is not in your path, and you do not want it to be, simply set the absolute path to the binary in your environment using `WEASYPRINT_BINARY`.
- Due to the addition of the scoped singleton, the service class is no longer immutable. Any method that previously cloned the service will no longer do so.
- Internally, the package now uses a [pipeline](https://github.com/mikerockett/pipeline) to prepare everything and call the WeasyPrint binary.

## 5.0.0 (Paradigm Release) `Unsupported`

### What’s New

- Adds full support for the [Laravel Service Container](https://laravel.com/docs/container) with a new service-based architecture
- [Laravel Octane](https://github.com/laravel/octane) compatibility
- A new config-based setup, instead of fluent helpers
- Adds the ability to pass the `--optimize-images` flag to WeasyPrint via the `optimizeImages` config option (requires v52 or greater)
- Adds the ability to save output as a file using Laravel’s [Filesystem](https://laravel.com/docs/filesystem) through the `putFile` method on the new `Output` object
- Improved explicit output types and implicit output type inference.

### Breaking Changes

Given that v5 is a paradigm release, the following changes are considered breaking. Whilst upgrade steps are shown here, they are not detailed, and so an [upgrade guide](upgrading.md) is also available for you to work through.

- The static `make` method is no longer available. Use `prepareSource($source)->build()` or `createFromSource($source)->build()` (when using [service-class instantiation](readme.me#option-1-service-class)) instead.
- The `view` method is also gone, and the package therefore no longer accepts data to pass to a view on your behalf. Instead, pass a `Renderable` hydrated with data (such as a [Laravel View](https://laravel.com/docs/views)) to `prepareSource`.
- Both `toPdf` and `toPng` no longer return the rendered data in raw form. They are merely shorthands for `to(OutputType::pdf())` and `to(OutputType::png())`, respectively. To get the data in raw form, call `build()->getData()`.
- The `download` and `inline` methods now return an instance of `Symfony\Component\HttpFoundation\StreamedResponse` instead of `Illuminate\Http\Response`.
- The `set*` and `add*` (except for `addAttachment`) configuration helpers are no longer available. Instead, pass the config into `new()` or `mergeConfig()`. The default config has been expanded to include all the possible options.

### Other Changes

- The `download` and `inline` methods may now be called either on the service or on the output returned from `build()`. If it is called on the service, `build()` will be called for you, with the file type inferred from the extension, which defaults to `.pdf` if not provided.

## 4.0.0 `Unsupported`

### Changes

- Drops support for PHP < 7.3
- Drops support for Laravel < 7.0
- Upgrades `orchestra/testbench` to v6
- Adds class coverage to test suite

## 3.0.0 `Unsupported`

### Changes

- Adds support for `symfony/process` v5

## 2.0.1 `Unsupported`

### Changes

- Adds support for Laravel 7

## 2.0.0 (Major Breaking Release) `Unsupported`

### Breaking Changes

- Drops the `convert` method in favor of only using `toPdf`, `toPng`, `download` or `inline`

### What’s New

- Adds more `set*` and `add*` fluent configuration helpers:
  - `addAttachment` to add an `--attachment`
  - `setResolution` to set the `--resolution`
  - `setMediaType` to set the `--media-type`
  - `setPresentationalHints` to toggle `--presentational-hints`
  - `setOutputEncoding` to set the `--encoding`
- Throws `InvalidOutputModeException` when the output mode is not `pdf` or `pdf`

## 1.0.5 `Unsupported`

### What’s New

- Adds support for URLs

### Changes

- Does not re-convert if the output is already available
- Adds the `toPdf` and `toPng` shorthand helpers

## 1.0.4 `Unsupported`

### What’s New

- Adds stylesheet support with `addStylesheet`

### Other Changes

- [Internal] Adds a GitLab CI pipeline for testing the package
- [Internal] Adds an ISC license file
- [Readme] Documents the `download` and `inline` methods

## 1.0.3 `Unsupported`

### What’s New

- Adds support for setting a base URL with `setBaseUrl`
- Adds `download` and `inline` helpers

### Other Changes

- [Internal] Adds proper tests

## 1.0.2 `Unsupported`

### Fixes

- Corrects the `view` method to be static, as intended

## 1.0.0 `Unsupported`

- Initial Release

</details>
