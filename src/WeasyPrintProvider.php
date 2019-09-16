<?php
namespace WeasyPrint;

use Illuminate\Support\ServiceProvider;

class WeasyPrintProvider extends ServiceProvider
{
  const packageConfig = '/../config/weasyprint.php';

  public function register()
  {
    $this->mergeConfigFrom(__DIR__ . static::packageConfig, 'weasyprint');
  }

  public function boot()
  {
    if ($this->app->runningInConsole()) {
      $this->publishes([
        __DIR__ . static::packageConfig => config_path('weasyprint.php')
      ], 'weasyprint-config');
    }
  }
}
