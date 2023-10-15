<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\Objects\Source;
use WeasyPrint\Service;

test('can prepare source from source', function (): void {
  $service = Service::new();
  $source = $service->prepareSource(
    Source::new('WeasyPrint rocks!')
  )->getSource();

  expect($source)->toBeInstanceOf(Source::class);
  expect($source->get())->toBeString();
});

test('can prepare source from arguments', function (): void {
  $service = Service::new();
  $source = $service->prepareSource('WeasyPrint rocks!')->getSource();

  expect($source)->toBeInstanceOf(Source::class);
  expect($source->get())->toBeString();
});

test('can prepare source from named arguments', function (): void {
  $service = Service::new();
  $source = $service->prepareSource('WeasyPrint rocks!')->getSource();

  expect($source)->toBeInstanceOf(Source::class);
  expect($source->get())->toBeString();
});

test('can prepare source from renderable', function (): void {
  $service = Service::new();
  $source = $service->prepareSource(view('test-pdf'))->getSource();

  expect($source->get())->toBeInstanceOf(Renderable::class);
});

test('can prepare source from url and verify', function (): void {
  $service = Service::new();
  $source = $service->prepareSource('https://google.com')->getSource();

  expect($source->isUrl())->toBeTrue();
});

test('can prepare source with attachments', function (): void {
  $service = Service::new();

  $source = $service->prepareSource('WeasyPrint rocks!')
    ->addAttachment($attachmentPath = __DIR__ . '/attachments/test-attachment.txt')
    ->getSource();

  expect($source->hasAttachments())->toBeTrue();
  expect($source->getAttachments()[0])->toEqual($attachmentPath);
});
