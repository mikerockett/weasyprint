<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Support\ServiceProvider;
use WeasyPrint\Contracts\Factory;

class Provider extends ServiceProvider
{
  public const SUPPORTED_VERSIONS = '^63.0|^64.0|^65.0|^66.0';

  public function register(): void
  {
    $this->app->scoped(Factory::class, Service::class);
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
