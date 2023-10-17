<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\Objects\Source;
use WeasyPrint\Service;

describe('can prepare source', function (): void {
  test('from source instance', function (): void {
    $service = Service::instance();
    $source = $service->prepareSource(
      new Source('WeasyPrint rocks!')
    )->getSource();

    expect($source)->toBeInstanceOf(Source::class);
    expect($source->get())->toBeString();
  });

  test('from argument', function (): void {
    $service = Service::instance();
    $source = $service->prepareSource('WeasyPrint rocks!')->getSource();

    expect($source)->toBeInstanceOf(Source::class);
    expect($source->get())->toBeString();
  });

  test('from renderable', function (): void {
    $service = Service::instance();
    $source = $service->prepareSource(view('test-pdf'))->getSource();

    expect($source->get())->toBeInstanceOf(Renderable::class);
  });

  test('from url', function (): void {
    $service = Service::instance();
    $source = $service->prepareSource('https://google.com')->getSource();

    expect($source->isUrl())->toBeTrue();
  });

  test('with attachments', function (): void {
    $service = Service::instance();

    $source = $service->prepareSource('WeasyPrint rocks!')
      ->addAttachment($attachmentPath = __DIR__ . '/attachments/test-attachment.txt')
      ->getSource();

    expect($source->hasAttachments())->toBeTrue();
    expect($source->getAttachments()[0])->toEqual($attachmentPath);
  });
});
