<img width="400" src="logo.png" alt="WeasyPrint for Laravel" />

---

A feature-rich Laravel wrapper for the [WeasyPrint Document Factory](https://weasyprint.org/).

**Note:** This package requires **Laravel 8+** running on **PHP 8+** in order to operate.

[Changelog](changelog.md) | [Upgrade Guide](upgrading.md)

# Table of Contents

- [Package Installation](#package-installation)
- [Service Instantiation](#service-instantiation)
  - [Option 1. Service Class](#option-1-service-class)
  - [Option 2. Dependency Injection](#option-2-dependency-injection)
  - [Option 3. Facade](#option-3-facade)
- [Building the Output](#building-the-output)
  - [Explicit Output Type](#explicit-output-type)
  - [Implicit Inference](#implicit-inference)
  - [Output Methods](#output-methods)
- [Preparing the Source](#preparing-the-source)
  - [Source Object](#source-object)
  - [String](#string)
  - [Attachments](#attachments)
- [Configuration](#configuration)
  - [Named Parameters and Argument Unpacking](#named-parameters-and-argument-unpacking)
  - [Merging](#merging)
  - [Available Configuration Options](#available-configuration-options)
- [Immutability](#immutability)
- [TL;DR, gimme a cheat-sheet!](#tldr-gimme-a-cheat-sheet)
- [Contributing](#contributing)
  - [Tests](#tests)
  - [Committing](#committing)
- [Open Source](#open-source)

## Package Installation

First make sure WeasyPrint is [installed on your system](https://weasyprint.readthedocs.io/en/latest/install.html).

Then install the package with Composer:

```shell
$ composer require rockett/weasyprint
```

The package will be discovered and registered automatically.

If you would like to publish the default configuration, you may run the following command:

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

To use dependency injection, you need to use the Factory contract, which will resolve the WeasyPrint service singleton from the [Service Container](https://laravel.com/docs/container). This singleton is prepared with your default configuration at framework boot-time, and is fully compatible with [Laravel Octane](https://github.com/laravel/octane).

The service singleton is immutable, which means that calls to `prepareSource`, `mergeConfig`, `addAttachment`, `to`, `toPdf`, and `toPng` will return a **newly cloned service instance**. See the notes on [immutability](#immutability) for more information.

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

Similar to dependency injection, using the Facade will give you an instance of the WeasyPrint service singleton. The Facade resolves to the Factory contract which, in turn, provides you with the singleton. From this point, the behaviour remains exactly as it would with dependency injection. That is, you will receive a newly cloned instance of the service when any of the prior methods are called. See the notes on [immutability](#immutability) for more information.

To change the configuration, you may call the `mergeConfig` method, just as you would with dependency injection.

## Building the Output

Now that you know how to instantiate a service class instance and prepare the source input, you are ready to build the output. This package supports everything WeasyPrint supports, which means you can build to PDF or PNG. This output type may be declared explicitly, or inferred implicitly from the filename.

### Explicit Output Type

```php
use WeasyPrint\Enums\OutputType;

$outputPdf = $service->to(OutputType::pdf())->build(); // this is the default
$outputPng = $service->to(OutputType::png())->build();

// Getting the output type from a string (useful when this is user-input)
$outputPng = $service->to(OutputType::from('png'))->build();
```

Here, we call the `to` method to set the output type, which must be an instance of the `OutputType` enumeration (Yes, we will support proper enums when PHP 8.1 is released 🎉). Then, we call `build` which spawns a Symfony Process that sends the source to `weasyprint` and returns the result to the service class.

The `build` method will return an instance of `WeasyPrint\Objects\Output`, which holds a resource pointing to the built output. With this, you may stream the content of the output as a download or an inline attachment, save it to a filesystem, or simply retrieve the string (more on this further down).

Note that `pdf` is the default output type. As such, calling `build` before declaring an output type will always output a PDF.

#### Shorthand

You may also set the output type using the shorthand methods:

```php
$outputPdf = $service->toPdf()->build();
$outputPng = $service->toPng()->build();
```

### Implicit Inference

```php
$outputPdf = $service->download('document.pdf');
$outputPng = $service->inline('document.png');
```

If you would prefer to not specify the output type and then build manually, you may simply call the appropriate return-method, like `download` or `inline` (discussed below). When you do this, the output type will be inferred from the file extension.

When doing this, the `to` and `build` methods will be called on your behalf, unless you have already called them.

### Output Methods

Once you have built the output, there are several things you can do with it:

1. Stream it as a download to the browser.

```php
$service->to(OutputType::pdf())->build()->download('document.pdf');
```

2. Inline it as an attachment.

```php
$service->to(OutputType::pdf())->build()->inline('document.pdf');
```

3. Save it to disk.

```php
$service->to(OutputType::pdf())->build()->putFile('documents/document.pdf', 's3', $options = []);
```

The `putFile` method, which forwards the operation to Laravel’s filesystem, accepts a the following arguments:

- A path relative to the root of your disk, not just a file name
- A specific Laravel Filesystem disk, such as `s3`. If you don’t provide this, the default disk will be used.
- Options to be passed to `put` on the disk instance.

4. Grab it and do what you want with it.

```php
$service->to(OutputType::pdf())->build()->getData(); // You can also call getContentType to get the MIME type.
```

Note that, in these examples, we are declaring the output type explicitly. This means that the implicit inference mechanism will not be triggered when you specify the file name. As such, if you were to provide an extension that does not match up with the output type, the package will just assume that you know what you're doing and proceed, no questions asked.

## Preparing the Source

With the basics out of the way, let’s talk more about preparing the source. The `prepareSource` method takes a single argument that represents your source data.

Here’s the method signature:

```php
public function prepareSource(
  WeasyPrint\Objects\Source|Illumintate\Support\Contracts\Renderable|string $source,
): static
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

### String

If you prefer to pass in a string:

```php
use WeasyPrint\Facade as WeasyPrint;

$service = WeasyPrint::prepareSource('<p>WeasyPrint rocks!</p>');
```

### Attachments

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

### Merging

No matter which way you pass in the configuration options, they will be merged with the defaults, which are acquired from the default configuration stored in the package source, or from the published config file if you ran `vendor:publish`.

### Available Configuration Options

Here are the configuration options you can set, along with their defaults:

```php
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
```

## Immutability

Under the hood, the service class is immutable. This means that every time you prepare a source, change configuration, or set the output format, the service is cloned. The idea here is that the base service is only created once per request, as a singleton, and actual conversion-related actions on the service are done in a fresh instance, created solely for the purpose of that conversion.

This provides additional benefits for stateful servers like [Laravel Octane](https://github.com/laravel/octane), where the base service is only ever created once with the default configuration. When a request terminates, cloned instances will be disposed of.

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

// Using Explicit Output Types
$service->to(OutputType::pdf())->build()->download('document.pdf')
$service->to(OutputType::from('pdf'))->build()->download('document.pdf')
$service->to(OutputType::png())->build()->inline('image.png')
$service->to(OutputType::pdf())->build()->getData()

// Using Explicit Output Types (Shorthand)
$service->toPdf()->build()->download('document.pdf')
$service->toPng()->build()->inline('document.png')
$service->toPdf()->build()->getData()

// Using Implicit Type Inference (OutputType determined by extension)
$service->download('document.pdf')
$service->inline('image.png')
$service->getData() // No filename here, so PDF will be used by default.
```

## Contributing

If you’d like to make a contribution to WeasyPrint for Laravel, you’re more than welcome to [submit a merge request](https://gitlab.com/mikerockett/weasyprint/-/merge_requests/new) against the master branch. Your request should be as detailed as possible, unless it’s a trivial change.

### Tests

Should it be required, please make sure that any impacted tests are updated, or new tests are created.

1. If you are introducing a new feature, you will more than likely need to create a new test case where each piece of fuctionality the new feature introduces may be tested.
2. Otherwise, if you are enhancing an existing feature by adding new functionality, you may add the appropriate test method to the applicable test case.

When building tests, you do not need to build them for each [instantiation type](#usage). Like other tests in the suite, you may use direct service-class instantiation.

Then run the tests before opening your merge request:

```shell
$ composer run test
```

### Committing

Your commit message should be clear and concise. If you’re fixing a bug, start the message with `bugfix:`. If it’s a feature: `feature:`. If it’s a chore, like formatting code: `chore:`.

If you’d simply like to report a bug or request a feature, simply [open an issue](https://gitlab.com/mikerockett/weasyprint/issues).

## Open Source

[Licensed under ISC](license.md), WeasyPrint for Laravel is an [open-source](http://opensource.com/resources/what-open-source) project, and is [free](https://en.wikipedia.org/wiki/Free_software) to use. In fact, it will always be open-source, and will always be free to use. Forever. 🎉

If you would like to support the development of WeasyPrint for Laravel, please consider [making a small donation via PayPal](https://paypal.me/mrockettza/20).
