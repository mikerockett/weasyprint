<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use WeasyPrint\Contracts\Factory;
use WeasyPrint\Service;

class Provider extends ServiceProvider
{
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

  public function register(): void
  {
    $this->app->scoped(Factory::class, fn ($app) => Service::new(
      ...$app->make(Repository::class)->get($this->name())
    ));
  }

  public function boot(): void
  {
    if ($this->app->runningInConsole()) {
      $this->publishes(
        [$this->configFile() => config_path('weasyprint.php')],
        $this->identifierFor('config')
      );
    }

    $this->mergeConfigFrom($this->configFile(), $this->name());
  }
}
