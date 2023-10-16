<!-- exclude-from-website: -->
# WeasyPrint for Laravel

![License](https://img.shields.io/badge/license-isc-green.svg?style=for-the-badge)
![Version](https://img.shields.io/packagist/v/rockett/weasyprint?label=Release&style=for-the-badge)
![Downloads](https://img.shields.io/packagist/dm/rockett/weasyprint?label=Installs&style=for-the-badge)
![Pipeline Status](https://img.shields.io/gitlab/pipeline/mikerockett/weasyprint/6.x?style=for-the-badge)

**A feature-rich Laravel wrapper for the [WeasyPrint Document Factory](https://weasyprint.org/).**

See the **[Changelog](changelog.md)** | View the **[Upgrade Guide](upgrading.md)**

---
- [Supported Versions](#supported-versions)
- [Installation](#installation)
- [Usage](#usage)
  - [Getting a Service Instance](#getting-a-service-instance)
  - [Preparing the Source](#preparing-the-source)
  - [Building the Output](#building-the-output)
  - [Attachments](#attachments)
  - [Config](#config)
- [Contributing](#contributing)
- [Open Source](#open-source)

---
<!-- /exclude-from-website -->
## Supported Versions

The table below outlines supported versions, where `current` means it the latest supported version, and `maintenance` is supported by way of bug and security fixes only.

> **Note:** As of version 8, WeasyPrint < 59 is not supported. If you rely on older versions, please use the applicable package version.

| Package Version     | WeasyPrint Version      | Laravel | PHP  | Branch                                                      |
| ------------------- | ----------------------- | ------- | ---- | ----------------------------------------------------------- |
| `8.x` (current)     | `^59.0\|^60.0` (semver) | 10.x    | 8.1+ | [8.x](https://gitlab.com/mikerockett/weasyprint/-/tree/8.x) |
| `7.x` (maintenance) | ≥ v53, < 59 (unchecked) | 10.x    | 8.1+ | [7.x](https://gitlab.com/mikerockett/weasyprint/-/tree/7.x) |

---

## Installation

First make sure **WeasyPrint v59+** is [installed on your system](https://doc.courtbouillon.org/weasyprint/latest/first_steps.html).

Then, install the package with Composer:

```shell
$ composer require rockett/weasyprint
```

The package will be discovered and registered automatically.

If you would like to publish the default configuration, you may run the command shown below. It’s not recommended to do this however, as the config file does read your environment variables. It is recommended to publish the config file only when you are changing the names of the variables, or you need to resolve them in another way.

```shell
$ php artisan vendor:publish --tag=weasyprint.config
```

## Usage

WeasyPrint for Laravel is easy to use.

All you need is an instance of the WeasyPrint service class, which can be resolved from the Laravel [Service Container](https://laravel.com/docs/container) as a [scoped singleton](https://laravel.com/docs/10.x/container#binding-scoped).

Once you have an instance, you can prepare your source, change config (if needed), and build the output for processing by your application.

### Getting a Service Instance

There are three ways to get an instance of the WeasyPrint service class.

#### Option 1. Using the Factory Contract

This contract may either be used in dependency injection, or via the global `app()` or `resolve()` helper functions.

**Dependency Injection:**

```php
use WeasyPrint\Contracts\Factory;

class GeneratePDF
{
  public function __invoke(Factory $weasyprint)
  {
    // You now have access to a WeasyPrint instance named $weasyprint.
    $weasyprint->prepareSource('<p>WeasyPrint rocks!</p>');
  }
}
```

**Global Helpers:**

```php
use WeasyPrint\Contracts\Factory;

$weasyprint = app(Factory::class); // or resolve(Factory::class)

// You now have access to a WeasyPrint instance named $weasyprint.
$weasyprint->prepareSource('<p>WeasyPrint rocks!</p>');
```

#### Option 2. Service Instance Helper

If you do not want to use dependency injection, and you do not want to use the `app()` or `resolve()` helpers, you can use the service instance helper instead. This is the same as calling one the global helpers.

```php
use WeasyPrint\Service;

$weasyprint = Service::instance();
$weasyprint->prepareSource('<p>WeasyPrint rocks!</p>');
```

#### Option 3. Facade

Similar to the other options, using the Facade will give you an instance of the WeasyPrint service singleton. The Facade resolves to the `Factory` contract which, in turn, provides you with the singleton.

```php
use WeasyPrint\Facade as WeasyPrint;

$source = '<p>WeasyPrint rocks!</p>';
$service = WeasyPrint::prepareSource($source);
```

> **Note:** For the sake of simplicity, the facade option will be used throughout the remainder of this documentation.

### Preparing the Source

Next step is to prepare the source – that is, the data WeasyPrint will use to render your PDF and return it to your application for processing.

The `prepareSource` method takes a single argument that represents your source data.

Here’s the method signature:

```php
use WeasyPrint\Objects\Source;
use Illumintate\Support\Contracts\Renderable;

public function prepareSource(Source|Renderable|string $source): static
```

The `$source` argument may be one of the following:

- `WeasyPrint\Objects\Source` if you are preparing a Source instance manually. The `Source::new` constructor accepts a `Renderable` or a `string`.
- `Illumintate\Support\Contracts\Renderable` if you are passing in an instance of something that is renderable, ie it implements the `render` method. This might be a Laravel View, which also accepts an array of data. For more information, see the Laravel [documentation on views](https://laravel.com/docs/views).
- `string` if you are passing in an already-rendered piece of HTML, or asking WeasyPrint to fetch and render the source from an external URL.

#### Source Object

If you would like to use the `Source` object, you may instantiate it as follows:

```php
use WeasyPrint\Objects\Source;
use WeasyPrint\Facade as WeasyPrint;

$source = Source::new('<p>WeasyPrint rocks!</p>');
$service = WeasyPrint::prepareSource($source);
```

#### Renderable

A Renderable is simply a class that implements the `Renderable` contract, described above.

```php
use Illumintate\Support\Contracts\Renderable;

class MyRenderable implements Renderable
{
  public function render(): string
  {
    return 'string with rendered HTML data…';
  }
}

// …

$service = WeasyPrint::prepareSource(new MyRenderable);
```

#### String

If you prefer to pass in a string:

```php
use WeasyPrint\Facade as WeasyPrint;

$service = WeasyPrint::prepareSource('<p>WeasyPrint rocks!</p>');
```

### Building the Output

Now that you know how to get a service class instance and prepare the source input, you are ready to build the output.

To do this, you can call the `build()` method, which will return an instance of `WeasyPrint\Objects\Output`.

```php
$output = $service->build();
```

The `$output` object makes the following methods available:

```php
public function download(string $filename, array $headers = [], bool $inline = false): StreamedResponse;
```

This method creates a Symfony `StreamedResponse` that may be used to download the PDF to the client (browser).

```php
public function inline(string $filename, array $headers = []): StreamedResponse;
```

Likewise, this method does the same, except it uses an inline attachment so that it may be displayed in the browser. This is just a shorthand for `download`, setting `$inline` to `true`.

```php
public function putFile(string $path, ?string $disk = null, array $options = []): bool;
```

This method forwards the data to Laravel’s [Filesystem](https://laravel.com/docs/filesystem) using the `Storage` facade’s `put` method, which gives you the ability to save the PDF to disk.

```php
public function getData(): string;
```

This method returns the raw PDF data as a string.

#### Implicit Inference

If you would prefer to not call `build()`, you can simply omit it and call the methods that are available on the `Output` class. The service will implicitly build the PDF for you, and then call the applicable method on the output.

### Attachments

WeasyPrint has the ability to add attachments to output PDFs. To add an attachment, call the `addAttachment` method:

```php
$service = WeasyPrint::prepareSource('<p>WeasyPrint rocks!</p>')
  ->addAttachment('/absolute/path/to/attachment');
  ->addAttachment('/as/many/as/you/like');
```

Naturally this has no effect when outputting to PNG.

### Config

This package comes preloaded with a sensible set of default configs. These defaults are sourced from the package directly, unless you [publish the config](#installation) into your application.

For additional type-safety, default config is prepared using the `Config` class. It must be cast to an `array`, and the service instance will automatically hydrate it when needed.

Once the default config has been loaded into a service instance, you can change it one of two ways:

**Tap the Config**

Tapping involves the use of a callback that mutates the existing `Config` object in the service instance, using the `tapConfig` method.

You need only change the properties you want to change, and everything else will remain untouched (derived from the defaults).

```php
use WeasyPrint\Objects\Config;

$service->tapConfig(
  static function (Config $config): void {
    $config->binary = '/absolute/path/to/weasyprint';
    $config->timeout = 5000;
  }
);
```

**Overriding the Config**

To override the config with a brand new `Config` object, you can call `setConfig`, which accepts the object and sets it on the service instance.

```php
use WeasyPrint\Objects\Config;

$service->setConfig(
  new Config(
    binary: '/absolute/path/to/weasyprint',
    timeout: 5000,
  )
);
```

> **Note:** Unlike tapping, overriding the config will disregard any defaults that you might have set in your config file, or those set in the unpublished config file. Instead, the defaults are derived from the constructor of `Config` object.

#### Config Defaults

Below are the default config options, which you can override per the above.

```php
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
  timeout: (int) env('WEASYPRINT_TIMEOUT', 120),

  /**
   * Force the input character encoding. utf-8 is recommended.
   */
  inputEncoding: env('WEASYPRINT_INPUT_ENCODING', 'utf-8'),

  /**
   * Enable or disable HTML Presentational Hints.
   */
  presentationalHints: (bool) env('WEASYPRINT_PRESENTATIONAL_HINTS', true),

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
   * Optionally provide an array of stylesheets to use alongside the HTML input.
   * Each stylesheet may the absolute path to a file, or a URL.
   * It is recommended to do this at runtime.
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
```

As noted before, you may publish the config file if you’d like to make changes to it – but, in most cases, you’ll want to make use of environment variables by adding them to your `.env` file or using whatever mechanism your app uses to resolve them.

## Contributing

If you’d like to make a contribution to WeasyPrint for Laravel, you’re more than welcome to [submit a merge request](https://gitlab.com/mikerockett/weasyprint/-/merge_requests/new) against the `main` or current-release branch:

1. If you are introducing a **non-breaking** change, target the `V.x` branch, where `V` is the latest major version of the package. If accepted and does not break any other versions either, it will also be merged into the applicable branches for those versions.
2. If you are introducing a **breaking** change of any kind, target the `main` branch. The change will be released in a new major version when accepted, and will not be added to older versions.

Your request should be as detailed as possible, unless it’s a trivial change.

#### Tests

Should it be required, please make sure that any impacted tests are updated, or new tests are created.

1. If you are introducing a new feature, you will more than likely need to create a new test case where each piece of functionality the new feature introduces may be tested.
2. Otherwise, if you are enhancing an existing feature by adding new functionality, you may add the appropriate test method to the applicable test case.

Then run the tests before opening your merge request:

```shell
$ composer run test
```

#### Formatting

This package uses `johnbacon/stout` to auto-format code. Before committing your code, please run a format over all dirty files:

```shell
$ composer run format
```

#### Commit Messages

Your commit message should be clear and concise. If you’re fixing a bug, start the message with `bugfix:`. If it’s a feature: `feature:`. If it’s a chore, like formatting code: `chore:`.

If you’d simply like to report a bug or request a feature, simply [open an issue](https://gitlab.com/mikerockett/weasyprint/issues).

## Open Source

[Licensed under ISC](license.md), WeasyPrint for Laravel is an [open-source](http://opensource.com/resources/what-open-source) project, and is [free](https://en.wikipedia.org/wiki/Free_software) to use. In fact, it will always be open-source, and will always be free to use. Forever. 🎉

If you would like to support the development of WeasyPrint for Laravel, please consider [making a small donation via PayPal](https://paypal.me/mrockettza/20).
