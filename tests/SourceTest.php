<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\Exceptions\AttachmentNotFoundException;
use WeasyPrint\Exceptions\SourceNotSetException;
use WeasyPrint\Objects\Source;
use WeasyPrint\Service;

describe('can prepare source', function (): void {
  test('from source instance', function (): void {
    $source = Service::instance()
      ->prepareSource(new Source('WeasyPrint rocks!'))
      ->getSource();

    expect($source)->toBeInstanceOf(Source::class);
    expect($source->get())->toBeString();
  });

  test('from argument', function (): void {
    $source = Service::instance()
      ->prepareSource('WeasyPrint rocks!')
      ->getSource();

    expect($source)->toBeInstanceOf(Source::class);
    expect($source->get())->toBeString();
  });

  test('from renderable', function (): void {
    $source = Service::instance()
      ->prepareSource(view('test-pdf'))
      ->getSource();

    expect($source->get())->toBeInstanceOf(Renderable::class);
  });

  test('from url', function (): void {
    $source = Service::instance()
      ->prepareSource('https://google.com')
      ->getSource();

    expect($source->isUrl())->toBeTrue();
  });

  test('with attachments', function (): void {
    $source = Service::instance()
      ->prepareSource('WeasyPrint rocks!')
      ->addAttachment($attachmentPath = __DIR__ . '/attachments/test-attachment.txt')
      ->getSource();

    expect($source->hasAttachments())->toBeTrue();
    expect($source->getAttachments()[0])->toEqual($attachmentPath);
  });
});

describe('exceptions', function (): void {
  test('source not set exception', function (): void {
    Service::instance()->build();
  })->throws(SourceNotSetException::class);

  test('attachment not found exception', function (): void {
    Service::instance()
      ->prepareSource('Attachment')
      ->addAttachment('/path/to/non-existent-file')
      ->build();
  })->throws(AttachmentNotFoundException::class);
});
