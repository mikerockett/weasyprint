<?php

declare(strict_types=1);

namespace WeasyPrint\Tests\Laravel\Support;

use Illuminate\Config\Repository;
use Illuminate\Support\Env;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\LaravelServiceProvider;
use WeasyPrint\Tests\Core\Support\PdfAssertions;

abstract class LaravelTestCase extends OrchestraTestCase
{
  use PdfAssertions;

  protected function getPackageProviders($app)
  {
    return [LaravelServiceProvider::class];
  }

  protected function getEnvironmentSetUp($app)
  {
    $app
      ->make(Repository::class)
      ->set('view.paths', [__DIR__ . '/../../views']);
  }

  protected function scopeEnv(
    string $envKey,
    mixed $envValue,
    callable $callback,
  ): void {
    $env = Env::getRepository();
    $env->set($envKey, $envValue);
    $callback($envKey);
    $env->clear($envKey);
  }

  protected function runOutputAssertions(
    mixed $output,
    string $expectedMime,
    string $expectedDisposition,
    array $expectedHeaders = [],
  ): void {
    $headers = $output->headers;
    $hasHeaderBag = $headers instanceof ResponseHeaderBag;

    expect($output instanceof StreamedResponse)->toBeTrue();
    expect($hasHeaderBag)->toBeTrue();

    if ($hasHeaderBag) {
      expect($headers->get('content-type'))->toEqual($expectedMime);
      expect($headers->get('content-disposition'))->toEqual($expectedDisposition);

      foreach ($expectedHeaders as $key => $value) {
        expect($headers->get($key))->toEqual($value);
      }
    }
  }
}
