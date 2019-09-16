# Laravel WeasyPrint

A simple wrapper for the [WeasyPrint PDF Engine](https://weasyprint.org/).

### Installation

```
composer require rockett/weasyprint
```

The package will be discovered and registered automatically.

### Usage

```php
use WeasyPrint\WeasyPrint;

$pdf = WeasyPrint::make(view('my-pdf-view'))->convert();
```

### Config

If you'd like to change the path to WeasyPrint or set the default timeout, you can publish the config file and make adjustments accordingly.

```shell
php artisan vendor:publish --tag=weasyprint-config
```
