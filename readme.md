# Laravel WeasyPrint

[![forthebadge](https://forthebadge.com/images/badges/made-with-crayons.svg)](https://forthebadge.com)
[![forthebadge](https://forthebadge.com/images/badges/does-not-contain-treenuts.svg)](https://forthebadge.com)

A simple Laravel 7.x/8.x wrapper for the [WeasyPrint Document Factory](https://weasyprint.org/).

## Installation

First make sure WeasyPrint is [installed on your system](https://weasyprint.readthedocs.io/en/latest/install.html). Then install the package with Composer:

```shell
$ composer require rockett/weasyprint
```

The package will be discovered and registered automatically.

## Usage

```php
use WeasyPrint\WeasyPrint;
```

### Input

```php
// Pass in a renderable, some text, or a URL …
$weasyprint = WeasyPrint::make(view('report'));
$weasyprint = WeasyPrint::make('Hello!');
$weasyprint = WeasyPrint::make('https://weasyprint.org');

// Or the name of a view …
$weasyprint = WeasyPrint::view('report');

// Perhaps some big data?
$weasyprint = WeasyPrint::view('report', [
  'bigData' => $this->getBigData()
]);
```

### Output

```php
// Render the file and get the data …
return $weasyprint->toPdf();
return $weasyprint->toPng();

// How about a direct download / inline render? (extension determines file type)
return $weasyprint->download('report.pdf');
return $weasyprint->inline('report.png');
```

### Other Options

```php
// Set a base URL for links …
$weasyprint->setBaseUrl('https://example.com/resources');

// Add a stylesheet …
$weasyprint->addStylesheet('https://fonts.googleapis.com/css?family=Roboto&display=swap')

// Less common, but handy when you need them …
$weasyprint->setResolution($resolution);
$weasyprint->setMediaType($mediaType);
$weasyprint->setPresentationalHints(true);
$weasyprint->addAttachment('/path/to/file');
```

## Config

If you'd like to change the path to the WeasyPrint binary or set the default cache prefix and process timeout, you can publish the config file and make adjustments accordingly.

```shell
php artisan vendor:publish --tag=weasyprint-config
```

This is the default configuration:

```php
return [
  'binary' => '/usr/local/bin/weasyprint',
  'cache_prefix' => 'weasyprint-cache_',
  'timeout' => 3600,
];
```

## Contributing

If you’d like to make a contribution to Laravel WeasyPrint, you’re more than welcome to [submit a merge request](https://gitlab.com/mikerockett/weasyprint/-/merge_requests/new) against the master branch. Your request should be as detailed as possible, unless it’s a trivial change.

Should it be required, please make sure that any impacted tests are updated, or new tests are created. Then run the tests before submitting your request to merge.

```shell
$ composer run-script test # or
$ ./vendor/bin/phpunit --testdox
```

Your commit message should be clear and concise. If you’re fixing a bug, start the message with `bugfix:`. If it’s a feature: `feature:`. If it’s a chore, like formatting code: `chore:`.

If you’d simply like to report a bug or request a feature, simply [open an issue](https://gitlab.com/mikerockett/weasyprint/issues).

## Open Source

[Licensed under ISC](license.md), Laravel WeasyPrint is an [open-source](http://opensource.com/resources/what-open-source) project, and is [free](https://en.wikipedia.org/wiki/Free_software) to use. In fact, it will always be open-source, and will always be free to use. Forever. 🎉

If you would like to support the development of Laravel WeasyPrint, please consider [making a small donation via PayPal](https://paypal.me/mrockettza/20).
