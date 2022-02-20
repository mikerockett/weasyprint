# WeasyPrint for Laravel – Release Notes

## 6.1.0 `current`, `minor`

This release adds support for Laravel 9, and works just fine with WeasyPrint v54.

## 6.0.0 (Breaking Release) `current`

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

## 5.0.0 (Paradigm Release) `maintenance`

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

## 4.0.0 `maintenance`

### Changes

- Drops support for PHP < 7.3
- Drops support for Laravel < 7.0
- Upgrades `orchestra/testbench` to v6
- Adds class coverage to test suite

## 3.0.0 `no support`

### Changes

- Adds support for `symfony/process` v5

## 2.0.1 `no support`

### Changes

- Adds support for Laravel 7

## 2.0.0 (Major Breaking Release) `no support`

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

## 1.0.5 `no support`

### What’s New

- Adds support for URLs

### Changes

- Does not re-convert if the output is already available
- Adds the `toPdf` and `toPng` shorthand helpers

## 1.0.4 `no support`

### What’s New

- Adds stylesheet support with `addStylesheet`

### Other Changes

- [Internal] Adds a GitLab CI pipeline for testing the package
- [Internal] Adds an ISC license file
- [Readme] Documents the `download` and `inline` methods

## 1.0.3 `no support`

### What’s New

- Adds support for setting a base URL with `setBaseUrl`
- Adds `download` and `inline` helpers

### Other Changes

- [Internal] Adds proper tests

## 1.0.2 `no support`

### Fixes

- Corrects the `view` method to be static, as intended

## 1.0.0 `no support`

- Initial Release
