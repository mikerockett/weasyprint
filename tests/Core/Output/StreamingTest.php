<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\StreamedResponse;
use WeasyPrint\Enums\StreamMode;
use WeasyPrint\WeasyPrintFactory;
use WeasyPrint\Tests\Fixtures\SampleHtml;

describe('output streaming', function (): void {
  it('returns a StreamedResponse from stream', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $response = $service->stream('test.pdf');

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect($response->headers->get('content-type'))->toBe('application/pdf');
    expect($response->headers->get('content-disposition'))->toBe('inline; filename=test.pdf');
  });

  it('returns attachment disposition from download', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $response = $service->download('test.pdf');

    expect($response->headers->get('content-disposition'))->toBe('attachment; filename=test.pdf');
  });

  it('returns inline disposition from inline', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $response = $service->inline('test.pdf');

    expect($response->headers->get('content-disposition'))->toBe('inline; filename=test.pdf');
  });

  it('accepts custom headers when streaming', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $response = $service->stream('test.pdf', ['X-Custom' => 'value']);

    expect($response->headers->get('X-Custom'))->toBe('value');
  });

  it('accepts stream mode parameter when streaming', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    $response = $service->stream('test.pdf', [], StreamMode::DOWNLOAD);

    expect($response->headers->get('content-disposition'))->toBe('attachment; filename=test.pdf');
  });
});
