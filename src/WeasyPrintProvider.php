<?php
namespace WeasyPrint;

use Illuminate\Support\ServiceProvider;

class WeasyPrintProvider extends ServiceProvider
{
  public function boot()
  {
    if ($this->app->runningInConsole()) {
      $this->publishes([
        __DIR__ . '/../config/weasyprint.php' => config_path('weasyprint.php')
      ], 'weasyprint-config');
    }
  }
}
