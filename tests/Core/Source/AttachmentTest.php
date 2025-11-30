<?php

declare(strict_types=1);

use WeasyPrint\Exceptions\SourceNotSetException;
use WeasyPrint\WeasyPrintFactory;
use WeasyPrint\Tests\Fixtures\SampleHtml;

describe('attachments', function (): void {
  test('can add attachment', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());
    $attachmentPath = __DIR__ . '/../../attachments/test-attachment.txt';
    $result = $service->addAttachment($attachmentPath);

    expect($result)->toBe($service); // fluent interface
    expect($service->getSource()->hasAttachments())->toBeTrue();
    expect($service->getSource()->getAttachments())->toHaveCount(1);
    expect($service->getSource()->getAttachments()[0])->toBe($attachmentPath);
  });

  test('can add multiple attachments', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());
    $attachmentPath = __DIR__ . '/../../attachments/test-attachment.txt';

    $service
      ->addAttachment($attachmentPath)
      ->addAttachment($attachmentPath);

    expect($service->getSource()->getAttachments())->toHaveCount(2);
  });

  test('hasAttachments() returns false when no attachments', function (): void {
    $service = new WeasyPrintFactory();
    $service->prepareSource(SampleHtml::simple());

    expect($service->getSource()->hasAttachments())->toBeFalse();
  });

  test('throws exception when adding attachment without source', function (): void {
    $service = new WeasyPrintFactory();
    $service->addAttachment('/some/file.txt');
  })->throws(Error::class, 'must not be accessed before initialization');

  test('throws exception when source not set on build', function (): void {
    $service = new WeasyPrintFactory();
    $service->build();
  })->throws(SourceNotSetException::class);
});
