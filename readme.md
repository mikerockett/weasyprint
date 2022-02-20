# WeasyPrint for Laravel

![License](https://img.shields.io/badge/license-isc-green.svg?style=for-the-badge)
![Version](https://img.shields.io/packagist/v/rockett/weasyprint?label=Release&style=for-the-badge)
![Downloads](https://img.shields.io/packagist/dm/rockett/weasyprint?label=Installs&style=for-the-badge)
![Pipeline Status](https://img.shields.io/gitlab/pipeline/mikerockett/weasyprint/6.x?style=for-the-badge)

**A feature-rich Laravel wrapper for the [WeasyPrint Document Factory](https://weasyprint.org/).**

This package requires at least **Laravel 8.47+** running on **PHP 8+** in order to operate. The reason a specific minor version of Laravel is required is due to the addition of [scoped singletons](https://laravel.com/docs/8.x/container#binding-scoped), which adds first-class support for [Laravel Octane](https://github.com/laravel/octane). In the previous version of this package, the singleton was immutable, which meant that every mutable-by-design method would actually return a cloned instance of the service.

See the **[Changelog](changelog.md)** | View the **[Upgrade Guide](upgrading.md)**

---
- [Supported WeasyPrint Versions](#supported-weasyprint-versions)
- [Package Installation](#package-installation)
- [Service Instantiation](#service-instantiation)
  - [Option 1. Service Class](#option-1-service-class)
  - [Option 2. Dependency Injection](#option-2-dependency-injection)
  - [Option 3. Facade](#option-3-facade)
- [Preparing the Source](#preparing-the-source)
  - [Source Object](#source-object)
  - [Renderable](#renderable)
  - [String](#string)
- [Building the Output](#building-the-output)
  - [Implicit Inference](#implicit-inference)
- [Attachments](#attachments)
- [Configuration](#configuration)
  - [Named Parameters and Argument Unpacking](#named-parameters-and-argument-unpacking)
  - [Merging with the Defaults](#merging-with-the-defaults)
  - [Available Configuration Options](#available-configuration-options)
- [TL;DR, gimme a cheat-sheet!](#tldr-gimme-a-cheat-sheet)
- [Contributing](#contributing)
  - [Tests](#tests)
  - [Commit Messages](#commit-messages)
- [Open Source](#open-source)

---

## Supported WeasyPrint Versions

There are two versions of the package that are supported. v6 is the latest, and is the only version that will receive new features. v5 is the previous, and will only receive bug-fixes and security-patches. The table below outlines supported versions:

The table below outlines supported versions:

| Package Version   | WeasyPrint    | Laravel                         | PHP | Branch                                                      |
| ----------------- | ------------- | ------------------------------- | --- | ----------------------------------------------------------- |
| `^6.0` (current)  | ≥ v53 (pydyf) | 8.47+ (scoped singletons), 9.0+ | 8.x | [6.x](https://gitlab.com/mikerockett/weasyprint/-/tree/6.x) |
| `^5.0` (previous) | < v53 (cairo) | 8.x (immutable singletons)      | 8.x | [5.x](https://gitlab.com/mikerockett/weasyprint/-/tree/5.x) |

**The guides below are for v6:**

## Package Installation

First make sure **WeasyPrint v53+** is [installed on your system](https://doc.courtbouillon.org/weasyprint/latest/first_steps.html).

Then, install the package with Composer:

```shell
$ composer require rockett/weasyprint
```

The package will be discovered and registered automatically.

If you would like to publish the default configuration, you may run the command shown below. It’s not recommended to do this however, as the config file does read your environment variables. It is recommended to publish the config file only when you are changing the names of the variables, or you need to resolve them in another way.

```shell
$ php artisan vendor:publish --tag=weasyprint.config
```

## Service Instantiation

WeasyPrint for Laravel provides different mechanisms you can use to get going. You may make use of the service class directly, use dependency injection, or use the Facade.

### Option 1. Service Class

```php
use WeasyPrint\Service as WeasyPrintService;

$source = '<p>WeasyPrint rocks!</p>';
$service = WeasyPrintService::new()->prepareSource($source);
```

This will give you a new WeasyPrint service class instance, ready to render a PDF or PNG based on the source provided to `prepareSource` (more on this further down).

When using the service class directly, the default configuration will be loaded in for you, unless you pass custom configuration into the `new` method, which is just a static alias to the constructor:

```php
$service = WeasyPrintService::new(
  binary: '/absolute/path/to/weasyprint',
  timeout: 5000,
);
```

Configuration options are discussed further down.

If you prefer a short-hand and don’t care much for changing any configuration, you can use the `createFromSource` static constructor instead:

```php
$service = WeasyPrintService::createFromSource($source);
```

### Option 2. Dependency Injection

```php
use WeasyPrint\Contracts\Factory as WeasyPrintFactory;

class GeneratePDF
{
  public function __invoke(WeasyPrintFactory $factory)
  {
    $source = '<p>WeasyPrint rocks!</p>';
    $service = $factory->prepareSource($source);
  }
}
```

To use dependency injection, you need to use the Factory contract, which will resolve the WeasyPrint service singleton from the [Service Container](https://laravel.com/docs/container). This singleton is scoped to ensure support for [Laravel Octane](https://github.com/laravel/octane).

To reconfigure this instance, you may call `mergeConfig` on the new service instance:

```php
$service->mergeConfig(
  binary: '/absolute/path/to/weasyprint',
  timeout: 5000,
)
```

You do not need to call this method at any specific point in time, but you must call it *before* you build the output.

### Option 3. Facade

```php
use WeasyPrint\Facade as WeasyPrint;

$source = '<p>WeasyPrint rocks!</p>';
$service = WeasyPrint::prepareSource($source);
```

Similar to dependency injection, using the Facade will give you an instance of the WeasyPrint service singleton. The Facade resolves to the Factory contract which, in turn, provides you with the singleton.

To change the configuration, you may call the `mergeConfig` method, just as you would with dependency injection.

## Preparing the Source

With the basics out of the way, let’s talk more about preparing the source. The `prepareSource` method takes a single argument that represents your source data.

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

### Source Object

If you would like to use the `Source` object, you may instantiate it as follows:

```php
use WeasyPrint\Objects\Source;
use WeasyPrint\Facade as WeasyPrint;

$source = Source::new('<p>WeasyPrint rocks!</p>');
$service = WeasyPrint::prepareSource($source);
```

### Renderable

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

### String

If you prefer to pass in a string:

```php
use WeasyPrint\Facade as WeasyPrint;

$service = WeasyPrint::prepareSource('<p>WeasyPrint rocks!</p>');
```

## Building the Output

Now that you know how to instantiate a service class instance and prepare the source input, you are ready to build the output. To do this, you can call the `build()` method, which will return an instance of `Objects\Output`.

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

### Implicit Inference

If you would prefer to not call `build()`, you can simply omit it and call the methods that are available on the `Output` class. The service will implicitly build the PDF for you, and then call the applicable method on the output.

## Attachments

WeasyPrint has the ability to add attachments to output PDFs. To add an attachment, call the `addAttachment` method:

```php
$service = WeasyPrint::prepareSource('<p>WeasyPrint rocks!</p>')
  ->addAttachment('/absolute/path/to/attachment');
  ->addAttachment('/as/many/as/you/like');
```

Naturally this has no effect when outputting to PNG.

## Configuration

As mentioned in previous sections, you may change WeasyPrint’s configuration on the fly, either by passing a configuration object to the `new` method of the service class, or by calling `mergeConfig` on an already-resolved service class instance.

### Named Parameters and Argument Unpacking

Both of these methods use argument unpacking to internally resolve a new instance of `WeasyPrint\Config`, which will be used by the service class instance to interpret the configuration options as and when needed.

Given that WeasyPrint for Laravel requires PHP 8 to run, you may pass in the configuration options as named arguments to either of these methods:

```php
$service->mergeConfig(
  binary: '/absolute/path/to/weasyprint',
  timeout: 5000,
);
```

If you prefer, however, you may also pass in an unpacked array:

```php
$service->mergeConfig(...[
  'binary' => '/absolute/path/to/weasyprint',
  'timeout' => 5000,
])
```

### Merging with the Defaults

No matter which way you pass in the configuration options, they will be merged with the defaults, which are acquired from the default configuration stored in the package source, or from the published config file if you ran `vendor:publish`.

### Available Configuration Options

Here are the configuration options you can set, along with their defaults:

```php
return [

  /**
   * The path to the WeasyPrint binary on your system.
   * If it is available on your system globally, the package will find and use it.
   * If not, then you will need to specify the absolute path.
   * @param string
   */
  'binary' => env('WEASYPRINT_BINARY'),

  /**
   * The environment variables passed to Symfony Process when
   * executing the WeasyPrint binary.
   * @param array
   */
  'processEnvironment' => ['LC_ALL' => env('WEASYPRINT_LOCALE', 'en_US.UTF-8')],

  /**
   * The cache prefix to use for the temporary filename.
   * @param string
   */
  'cachePrefix' => env('WEASYPRINT_CACHE_PREFIX', 'weasyprint_cache'),

  /**
   * The amount of seconds to allow a conversion to run for.
   * @param int
   */
  'timeout' => env('WEASYPRINT_TIMEOUT', 120),

  /**
   * Force the input character encoding. utf-8 is recommended.
   * @param string
   */
  'inputEncoding' => env('WEASYPRINT_INPUT_ENCODING', 'utf-8'),

  /**
   * Enable or disable HTML Presentational Hints.
   * When enabled, `--presentational-hints` is passed to the binary.
   * @param bool
   */
  'presentationalHints' => env('WEASYPRINT_PRESENTATIONAL_HINTS', true),

  /**
   * Optionally set the media type to use for CSS @media.
   * Defaults to `print` at binary-level.
   * @param string|null
   */
  'mediaType' => env('WEASYPRINT_MEDIA_TYPE'),

  /**
   * Optionally set the base URL for relative URLs in the HTML input.
   * Defaults to the input’s own URL at binary-level.
   * @param string|null
   */
  'baseUrl' => env('WEASYPRINT_BASE_URL'),

  /**
   * Optionally provide an array of stylesheets to use alongside the HTML input.
   * Each stylesheet may the absolute path to a file, or a URL.
   * It is recommended to do this at runtime.
   * @param string[]|null
   */
  'stylesheets' => null,

  /**
   * Optionally enable size optimizations, where WeasyPrint will attempt
   * to reduce the size of embedded images, fonts or both.
   * Use: 'images', 'fonts', 'all' or 'none' (default)
   * @param string
   */
  'optimizeSize' => env('WEASYPRINT_OPTIMIZE_SIZE', 'none'),

];
```

As noted before, you may publish the config file if you’d like to make changes to it – but, in most cases, you’ll want to make use of environment variables by adding them to your `.env` file or using whatever mechanism your app uses to resolve them.

## TL;DR, gimme a cheat-sheet!

Here's a cheat-sheet showing all possible approaches and scenarios.

```php
// Managing Config
$service = WeasyPrint\Service::new(binary: '/bin/weasyprint');
$service = WeasyPrint\Service::new(...['binary' => '/bin/weasyprint']);
$service = WeasyPrint\Facade::mergeConfig(binary: '/bin/weasyprint');
$service = WeasyPrint\Facade::mergeConfig(...['binary' => '/bin/weasyprint']);

// Preparing the Source
$service = WeasyPrint\Service::new()->prepareSource('Cheat-sheet!');
$service = WeasyPrint\Service::createFromSource('Cheat-sheet!');
$service = WeasyPrint\Facade::prepareSource('Cheat-sheet!');
$service = app(WeasyPrint\Factory::class)::prepareSource('Cheat-sheet!');

// Using Explicit calls to build()
$service->build()->download('document.pdf');
$service->build()->inline('document.pdf');
$service->build()->putFile('document.pdf', 'disk-name');
$service->build()->getData();

// Using Implicit Output Inference
$service->download('document.pdf');
$service->inline('document.pdf');
$service->putFile('document.pdf', 'disk-name');
$service->getData();
```

## Contributing

If you’d like to make a contribution to WeasyPrint for Laravel, you’re more than welcome to [submit a merge request](https://gitlab.com/mikerockett/weasyprint/-/merge_requests/new) against the `main` or current-release branch:

1. If you are introducing a **non-breaking** change and supports WeasyPrint **< v53 (cairo)**, target the `5.x` branch.
2. If you are introducing a **non-breaking** change and supports WeasyPrint **≥ v53 (pydyf)**, target the `6.x` branch.
3. If you are introducing a **breaking** change of any kind, target the `main` branch. The change will be released in a new major version when accepted.

Your request should be as detailed as possible, unless it’s a trivial change.

### Tests

Should it be required, please make sure that any impacted tests are updated, or new tests are created.

1. If you are introducing a new feature, you will more than likely need to create a new test case where each piece of fuctionality the new feature introduces may be tested.
2. Otherwise, if you are enhancing an existing feature by adding new functionality, you may add the appropriate test method to the applicable test case.

When building tests, you do not need to build them for each [instantiation type](#service-instantiation). Like other tests in the suite, you may use direct service-class instantiation.

Then run the tests before opening your merge request:

```shell
$ composer run test
```

This will run tests in parallel. To run them sequentially, run this instead:

```shell
$ ./vendor/bin/testbench package:test
```

### Commit Messages

Your commit message should be clear and concise. If you’re fixing a bug, start the message with `bugfix:`. If it’s a feature: `feature:`. If it’s a chore, like formatting code: `chore:`.

If you’d simply like to report a bug or request a feature, simply [open an issue](https://gitlab.com/mikerockett/weasyprint/issues).

## Open Source

[Licensed under ISC](license.md), WeasyPrint for Laravel is an [open-source](http://opensource.com/resources/what-open-source) project, and is [free](https://en.wikipedia.org/wiki/Free_software) to use. In fact, it will always be open-source, and will always be free to use. Forever. 🎉

If you would like to support the development of WeasyPrint for Laravel, please consider [making a small donation via PayPal](https://paypal.me/mrockettza/20).
