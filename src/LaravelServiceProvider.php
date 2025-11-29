<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Support\ServiceProvider;
use WeasyPrint\Contracts\Factory;

class LaravelServiceProvider extends ServiceProvider
{
  public function register(): void
  {
    $this->app->scoped(Factory::class, fn($app) => new Service(
      data_get($app, 'config.weasyprint', []),
    ));
  }

  public function boot(): void
  {
    if ($this->app->runningInConsole()) {
      $this->publishes(
        paths: [$this->configFile() => config_path("{$this->name()}.php")],
        groups: $this->identifierFor('config'),
      );
    }

    $this->mergeConfigFrom(
      path: $this->configFile(),
      key: $this->name(),
    );
  }

  protected function name(): string
  {
    return 'weasyprint';
  }

  protected function identifierFor(string $suffix): string
  {
    return "{$this->name()}.$suffix";
  }

  protected function configFile(): string
  {
    return __DIR__ . '/../config/weasyprint.php';
  }
}
