<?php

declare(strict_types=1);

namespace WeasyPrint\Tests;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Env;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Provider;

abstract class TestCase extends OrchestraTestCase
{
  protected function getPackageProviders($app)
  {
    return [Provider::class];
  }

  /** @param  Application  $app */
  protected function getEnvironmentSetUp($app)
  {
    $app
      ->make(Repository::class)
      ->set('view.paths', [__DIR__ . '/views']);
  }

  public function scopeEnv(
    string $envKey,
    mixed $envValue,
    callable $callback,
  ): void {
    $env = Env::getRepository();
    $env->set($envKey, $envValue);
    $callback($envKey);
    $env->clear($envKey);
  }

  public function writeTempFile(string $contents): string
  {
    file_put_contents(
      filename: $tempFilename = tempnam(
        sys_get_temp_dir(),
        config('weasyprint.cachePrefix', 'weasyprint_cache')
      ),
      data: $contents
    );

    return $tempFilename;
  }

  public function runPdfAssertions(string $output): void
  {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tempFilename = $this->writeTempFile($output);
    $mime = finfo_file($finfo, $tempFilename);

    expect($output)->not->toBeNull();
    expect($output)->not->toBeEmpty();
    expect($mime)->toEqual('application/pdf');

    $parser = new Parser();
    $document = $parser->parseFile($tempFilename);

    expect($document->getDetails()['Producer'])->toStartWith('WeasyPrint');
    unlink($tempFilename);
    expect(is_file($tempFilename))->toBeFalse();
  }

  public function runOutputAssertions(
    mixed $output,
    string $expectedMime,
    string $expectedDisposition
  ): void {
    $headers = $output->headers;
    $hasHeaderBag = $headers instanceof ResponseHeaderBag;

    expect($output instanceof StreamedResponse)->toBeTrue();
    expect($hasHeaderBag)->toBeTrue();

    if ($hasHeaderBag) {
      expect($headers->get('content-type'))->toEqual($expectedMime);
      expect($headers->get('content-disposition'))->toEqual($expectedDisposition);
    }
  }
}
