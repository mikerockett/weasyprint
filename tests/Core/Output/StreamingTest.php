<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Enums\StreamMode;
use WeasyPrint\WeasyPrintFactory;
use WeasyPrint\Tests\Fixtures\SampleHtml;

describe('output streaming', function (): void {
  test('factory stream() returns StreamedResponse', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $response = $service->stream('test.pdf');

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect($response->headers->get('content-type'))->toBe('application/pdf');
    expect($response->headers->get('content-disposition'))->toBe('inline; filename=test.pdf');
  });

  test('factory download() returns attachment disposition', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $response = $service->download('test.pdf');

    expect($response->headers->get('content-disposition'))->toBe('attachment; filename=test.pdf');
  });

  test('factory inline() returns inline disposition', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $response = $service->inline('test.pdf');

    expect($response->headers->get('content-disposition'))->toBe('inline; filename=test.pdf');
  });

  test('factory stream() accepts custom headers', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $response = $service->stream('test.pdf', ['X-Custom' => 'value']);

    expect($response->headers->get('X-Custom'))->toBe('value');
  });

  test('factory stream() accepts stream mode parameter', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $response = $service->stream('test.pdf', [], StreamMode::DOWNLOAD);

    expect($response->headers->get('content-disposition'))->toBe('attachment; filename=test.pdf');
  });
});
