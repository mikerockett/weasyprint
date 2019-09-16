# Laravel WeasyPrint

A simple wrapper for the [WeasyPrint PDF Engine](https://weasyprint.org/). Requires Laravel 5.8/6.0+.

### Installation

```
composer require rockett/weasyprint
```

The package will be discovered and registered automatically.

### Usage

```php
use WeasyPrint\WeasyPrint;

// Pass in a view or a URL …
$pdf = WeasyPrint::make(view('my-pdf-view'))->convert();
$pdf = WeasyPrint::make(file_get_contents('https://weasyprint.org'))->convert();

// Or the name of a view …
$pdf = WeasyPrint::view('my-pdf-view')->convert();

// Perhaps some big data?
$pdf = WeasyPrint::view('my-pdf-view', [
  'data' => $this->getBigData()
])->convert();

// Would you like a PNG?
$png = WeasyPrint::view('my-png-view')->convert('png');
```

### Config

If you'd like to change the path to WeasyPrint or set the default timeout, you can publish the config file and make adjustments accordingly.

```shell
php artisan vendor:publish --tag=weasyprint-config
```
