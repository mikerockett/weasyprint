<?php

declare(strict_types=1);

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\Contracts\Factory;
use WeasyPrint\Exceptions\AttachmentNotFoundException;
use WeasyPrint\Exceptions\SourceNotSetException;
use WeasyPrint\Objects\Source;

describe('can prepare source', function (): void {
  test('from source instance', function (): void {
    $source = app(Factory::class)
      ->prepareSource(new Source('WeasyPrint rocks!'))
      ->getSource();

    expect($source)->toBeInstanceOf(Source::class);
    expect($source->get())->toBeString();
  });

  test('from argument', function (): void {
    $source = app(Factory::class)
      ->prepareSource('WeasyPrint rocks!')
      ->getSource();

    expect($source)->toBeInstanceOf(Source::class);
    expect($source->get())->toBeString();
  });

  test('from renderable', function (): void {
    $source = app(Factory::class)
      ->prepareSource(view('test-pdf'))
      ->getSource();

    expect($source->get())->toBeInstanceOf(Renderable::class);
  });

  test('from url', function (): void {
    $source = app(Factory::class)
      ->prepareSource('https://google.com')
      ->getSource();

    expect($source->isUrl())->toBeTrue();
  });

  test('with attachments', function (): void {
    $source = app(Factory::class)
      ->prepareSource('WeasyPrint rocks!')
      ->addAttachment($attachmentPath = __DIR__ . '/attachments/test-attachment.txt')
      ->getSource();

    expect($source->hasAttachments())->toBeTrue();
    expect($source->getAttachments()[0])->toEqual($attachmentPath);
  });
});

describe('exceptions', function (): void {
  test('source not set exception', function (): void {
    app(Factory::class)->build();
  })->throws(SourceNotSetException::class);

  test('attachment not found exception', function (): void {
    app(Factory::class)
      ->prepareSource('Attachment')
      ->addAttachment('/path/to/non-existent-file')
      ->build();
  })->throws(AttachmentNotFoundException::class);
});
