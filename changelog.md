<img width="400" src="logo.png" alt="WeasyPrint for Laravel" />

---

# Release Notes

## v5.0.0 (Major Paradigm Release) `current`

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

## v4.0.0 `maintenance`

### Changes

- Drops support for PHP < 7.3
- Drops support for Laravel < 7.0
- Upgrades `orchestra/testbench` to v6
- Adds class coverage to test suite

## v3.0.0 `no support`

### Changes

- Adds support for `symfony/process` v5

## v2.0.1 `no support`

### Changes

- Adds support for Laravel 7

## v2.0.0 (Major Breaking Release) `no support`

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

## v1.0.5 `no support`

### What’s New

- Adds support for URLs

### Changes

- Does not re-convert if the output is already available
- Adds the `toPdf` and `toPng` shorthand helpers

## v1.0.4 `no support`

### What’s New

- Adds stylesheet support with `addStylesheet`

### Other Changes

- [Internal] Adds a GitLab CI pipeline for testing the package
- [Internal] Adds an ISC license file
- [Readme] Documents the `download` and `inline` methods

## v1.0.3 `no support`

### What’s New

- Adds support for setting a base URL with `setBaseUrl`
- Adds `download` and `inline` helpers

### Other Changes

- [Internal] Adds proper tests

## v1.0.2 `no support`

### Fixes

- Corrects the `view` method to be static, as intended

## v1.0.0 `no support`

- Initial Release
