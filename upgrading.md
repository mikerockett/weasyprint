# WeasyPrint for Laravel – Upgrade Guide

## v5 → v6

Version 6 of the package is a trimmed down version of v5, with specific emphasis on support for WeasyPrint v53, which has a new rendering engine (drops cairo) and no longer supports PNG images.

To upgrade to v6 of the package, you must be running WeasyPrint v53 or greater. A varity of installation options are available on their [documentation](https://doc.courtbouillon.org/weasyprint/latest/first_steps.html).

This version drops support for output types, as only PDFs are supported now. This means that the `to()`, `toPdf()` and `toPng()` methods have been dropped. The `OutputType` enumeration class has also been dropped.

The instantiation options are still available, however you no longer need to call any of the above methods. For example, assuming you are using the Facade, you can now do the following:

```php
// Download
WeasyPrint::prepareSource($source)->build()->download('filename.pdf'); // or,
WeasyPrint::prepareSource($source)->download('filename.pdf');

// Inline
WeasyPrint::prepareSource($source)->build()->inline('filename.pdf'); // or,
WeasyPrint::prepareSource($source)->inline('filename.pdf');

// Get Raw Data
$data = WeasyPrint::prepareSource($source)->build()->getData(); // or,
$data = WeasyPrint::prepareSource($source)->getData();
```

Be sure to checkout the [readme](readme.md) to see all the instantiation approaches.

Additionally, some configuration options have changed:

- `resolution` has been dropped – this was for PNGs only. You may remove this option if you have published your config file.
- `optimizeImages` has been dropped in favor of `optimizeSize`, corresponding to the `--optimize-size` flag that is passed to WeasyPrint. You don't need to do anything if you have not published your config file. If you have, simply remove `optimizeImages` and replace it with `optimizeSize`, as follows:

```php
return [
  // …

  /**
   * Optionally enable size optimizations, where WeasyPrint will attempt
   * to reduce the size of embedded images, fonts or both.
   * Use: 'images', 'fonts', 'all' or 'none' (default)
   * @param string
   */
  'optimizeSize' => env('WEASYPRINT_OPTIMIZE_SIZE', 'none'),
];
```

Note that the configuration now supports environment variables, which means you don't need to publish the config file anymore, unless you'd like to change the variable names or resolve them in a different way. See the [readme](readme.md) for more information.

And, lastly, the `getContentType()` method on the `Output` class has been removed – you will always receive `application/pdf`.

## v2/3/4 → v5.0.0

Being a total rewrite of the package, v5 is a Major Paradigm Release with several breaking changes. In this guide, the changes and upgrade paths are documented.

### Instantiation and preparing the source

The service class was previously instantiated with a static call to either `make` or `view`. These methods created a new instance of the `WeasyPrint` class, hydrated with the relevant “source”. In v5, both of these methods have been dropped as part of the change in architecture.

WeasyPrint for Laravel now supports instantiation via the Laravel Service Container or directly via the static `new()` method on the `Service` class, which has replaced the old `WeasyPrint` class.

As a result, there are now three methods you can use to get an instance of the `Service` class, and only one method to prepare the source. The readme covers the options available, however the diff below provides some quick examples of how to upgrade:

#### From `make` to `new()->prepareSource()` using service-instantiation

```diff
-- $service = WeasyPrint\WeasyPrint::make('<p>Test</p>');
++ $service = WeasyPrint\Service::new()->prepareSource('<p>Test</p>');
```

#### From `make` to `prepareSource()` using the service container

```diff
-- $service = WeasyPrint\WeasyPrint::make('<p>Test</p>');
++ $service = app(WeasyPrint\Factory::class)->prepareSource('<p>Test</p>');
```

Naturally, `Factory` may be resolved through dependency injection. In the example below, a cloned instance of the service is being returned. See the notes on [immutability](readme.md#immutability) for more information.

```php
public function __invoke(WeasyPrint\Factory $weasyprint)
{
  $service = $weasyprint->prepareSource('<p>Test</p>');
}
```

#### From `make` to `prepareSource()` using the facade

```diff
-- $service = WeasyPrint\WeasyPrint::make('<p>Test</p>');
++ $service = WeasyPrint\Facade::prepareSource('<p>Test</p>');
```

---
> 💡 **Note:** From this point onwards, this guide will use service-instantiation for all upgrade paths.
---

#### From `view` to `new()->prepareSource()` (without data)

```diff
-- $service = WeasyPrint\WeasyPrint::view('my-view');
++ $service = WeasyPrint\Service::new()->prepareSource(view('my-view'));
```
#### From `view` to `new()->prepareSource()` (with data)

```diff
$data = ['foo' => 'bar'];

-- $service = WeasyPrint\WeasyPrint::view('my-view', $data);
++ $service = WeasyPrint\Service::new()->prepareSource(view('my-view', $data));
```

### Configuration

In v5, the `set*` and `add*` (except for `addAttachment`) helpers are no longer available. Instead, the package uses a config-based architecture, where configuration is defined through arrays via argument unpacking, as well as named arguments when setting config directly.

When the service provider boots up, it merges the default configuration (either from the package or the config file published with `vendor:publish --tag=weasyprint.config`) into the Laravel config repository, available in the service container.

Whenever a new `Service` is instantiated, it will read this config using argument unpacking and will merge any specific overrides you pass in. These overrides may be passed in as an unpacked array or as named arguments.

For the purposes of this guide, only named arguments will be shown. For additional brevity, the example below shows all configuration options being changed in a single call.

```diff
-- $service = WeasyPrint\WeasyPrint::view('my-view')
--   ->setTimeout(10000)
--   ->setBaseUrl('https://example.com')
--   ->setResolution(300)
--   ->setMediaType('print')
--   ->addStylesheet('https://example.com/styles.css')
--   ->setPresentationalHints(true)
--   ->setOutputEncoding('utf-8');
++ $service = WeasyPrint\Service::new(
++   timeout: 10000,
++   baseUrl: 'https://example.com',
++   resolution: 300,
++   mediaType: 'print',
++   stylesheets: ['https://example.com/styles.css']
++   presentationalHints: true,
++   inputEncoding: 'utf-8',
++ )->prepareSource(view('my-view'))
```

However, it’s strongly advised to only override whatever needs changing as all of these options are now set in the config file. In previous versions, this was not the case and only `binary`, `cache_prefix` and `timeout` were configurable from the config file.

> 💡 Additionally, `binary` and `cache_prefix` were not configurable after the service was instantiated (unless the config was changed in memory using Laravel’s config repository, which no longer has any effect).

An optimal approach would be to publish the config file through `vendor:publish --tag=weasyprint.config` and make the changes there. Where specific changes are needed for specific builds (conversions), you should then make use of passing config options to `new()` or `mergeConfig` when using dependency injection or the facade:

```php
$service = app(WeasyPrint\Factory::class)->mergeConfig(binary: '/path/to/weasyprint');
$service = WeasyPrint\Facade::mergeConfig(binary: '/path/to/weasyprint');
```

> 💡 If you have already published your config file, it is recommended that you delete it, re-publish and ensure your previous changes are made in the new config file.

### Building and dealing with output

In previous versions, a call to any method that triggered a conversion would return the output data directly. This meant that calling `toPdf`, `toPng`, `download` or `inline` would trigger the conversion and return the result in the manner intended.

This changes in v5. Instead of doing two things at once, the package now breaks the process into three distinct steps.

> 💡 There are, however, shorthands available to perform these steps together, should you prefer the simpler syntax.

1. The first step is to specify the output type using the `OutputType` enumeration, or with the shorthand `toPdf` and `toPng` methods. This step may be skipped in favor of implicit output type inference with the `download`, `inline` and `putFile` helpers. If you want raw data and you do not specify an output type, it will default to PDF.
2. The second step is to build the output using `build()` on the service instance. This triggers a pipeline that does some preflight checks, sends the input to the WeasyPrint binary, and returns an `Output` object.
3. With an `Output` object in-hand, the third step is to get the raw data, or stream it as a download or an inline attachment, or save it to disk.

The diffs below show what needs to be changed. Each of them show the longhand and shorthand approaches you can take:

#### Getting the Raw Data

```diff
-- $pdfData = WeasyPrint\WeasyPrint::make('<p>Test</p>')->toPdf();
```

##### Longhand:

```diff
++ $pdfData = WeasyPrint\Service::new()
++   ->prepareSource('<p>Test</p>')
++   ->to(WeasyPrint\Enums\OutputType::pdf())
++   ->build()
++   ->getData();
```

##### Shorthand for output type call:

```diff
++ $pdfData = WeasyPrint\Service::new()
++   ->prepareSource('<p>Test</p>')
--   ->to(WeasyPrint\Enums\OutputType::pdf())
++   ->toPdf()
++   ->build()
++   ->getData(); // or download() or inline() or putFile()
```

##### OR, Static shorthand with explicit output type inference (only applicable to service-instantiation):
```diff
++ $stream = WeasyPrint\Service::createFromSource('<p>Test</p>')
++   ->toPdf()
++   ->getData()
```

#### Streaming to File

```diff
-- $pdfData = WeasyPrint\WeasyPrint::make('<p>Test</p>')->download('document.pdf'); // OR
-- $pdfData = WeasyPrint\WeasyPrint::make('<p>Test</p>')->inline('document.pdf');
```

##### Longhand:

```diff
++ $pdfData = WeasyPrint\Service::new()
++   ->prepareSource('<p>Test</p>')
++   ->to(WeasyPrint\Enums\OutputType::pdf())
++   ->build()
++   ->download('document.pdf'); // or inline() or putFile()
```

##### OR, Shorthand for output type call:

```diff
++ $pdfData = WeasyPrint\Service::new()
++   ->prepareSource('<p>Test</p>')
--   ->to(WeasyPrint\Enums\OutputType::pdf())
++   ->toPdf()
++   ->build()
++   ->download('document.pdf'); // or inline() or putFile()
```

##### OR, Implicit output type inference:

```diff
++ $stream = WeasyPrint\Service::new()
++   ->prepareSource('<p>Test</p>')
--   ->toPdf()
--   ->build()
++   ->download('document.pdf'); // or inline() or putFile()
```

##### OR, Static shorthand with implicit output type inference (only applicable to service-instantiation):
```diff
-- $stream = WeasyPrint\Service::new()
--   ->prepareSource('<p>Test</p>')
--   ->download('document.pdf');
++ $stream = WeasyPrint\Service::createFromSource('<p>Test</p>')
++   ->download('document.pdf') // or inline() or putFile();
```
