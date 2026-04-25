<?php

declare(strict_types=1);

use WeasyPrint\Contracts\WeasyPrint;
use WeasyPrint\Enums\StreamMode;
use WeasyPrint\Exceptions\SourceNotSetException;
use WeasyPrint\Integration\Laravel\WeasyPrint as WeasyPrintFacade;
use WeasyPrint\Testing\FakeWeasyPrint;

describe('fake weasyprint', function (): void {
  test('facade fake() swaps binding', function (): void {
    $fake = WeasyPrintFacade::fake();

    expect($fake)->toBeInstanceOf(FakeWeasyPrint::class);
    expect(app(WeasyPrint::class))->toBe($fake);
  });

  test('build returns fake output without invoking binary', function (): void {
    $fake = WeasyPrintFacade::fake();

    $output = $fake->prepareSource('<p>test</p>')->build();

    expect($output->data)->toContain('PDF');
  });

  test('getData returns fake data', function (): void {
    $fake = WeasyPrintFacade::fake();

    $data = $fake->prepareSource('<p>test</p>')->getData();

    expect($data)->toBeString();
    expect($data)->toContain('PDF');
  });

  test('getWeasyPrintVersion returns fake version', function (): void {
    $fake = WeasyPrintFacade::fake();

    expect($fake->getWeasyPrintVersion())->toBe('68.0');
  });

  test('assertBuilt passes when build was called', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>test</p>')->build();

    $fake->assertBuilt();
  });

  test('assertBuilt checks count', function (): void {
    $fake = WeasyPrintFacade::fake();
    $fake->prepareSource('<p>test</p>');

    $fake->build();
    $fake->build();

    $fake->assertBuilt(2);
  });

  test('assertNotBuilt passes when build was not called', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->assertNotBuilt();
  });

  test('assertSourcePrepared passes when source was prepared', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>hello</p>');

    $fake->assertSourcePrepared();
  });

  test('assertSourcePrepared accepts callback', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>hello</p>');

    $fake->assertSourcePrepared(function ($source) {
      expect($source)->toBe('<p>hello</p>');
    });
  });

  test('assertDownloaded passes when download was called', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>test</p>')->download('invoice.pdf');

    $fake->assertDownloaded();
    $fake->assertDownloaded('invoice.pdf');
  });

  test('assertInlined passes when inline was called', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>test</p>')->inline('report.pdf');

    $fake->assertInlined();
    $fake->assertInlined('report.pdf');
  });

  test('assertStreamed passes when stream was called', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>test</p>')->stream('doc.pdf', [], StreamMode::DOWNLOAD);

    $fake->assertStreamed();
    $fake->assertStreamed('doc.pdf');
    $fake->assertStreamed(mode: StreamMode::DOWNLOAD);
  });

  test('assertAttachmentAdded passes when attachment was added', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>test</p>')->addAttachment('/path/to/file.xml', 'Data');

    $fake->assertAttachmentAdded();
    $fake->assertAttachmentAdded('/path/to/file.xml');
  });

  test('addAttachment throws SourceNotSetException when source is missing', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->addAttachment('/path/to/file.xml');
  })->throws(SourceNotSetException::class);

  test('assertXmpMetadataAdded passes when xmp metadata was added', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->addXmpMetadata('/path/to/rdf.xml');

    $fake->assertXmpMetadataAdded();
    $fake->assertXmpMetadataAdded('/path/to/rdf.xml');
  });

  test('download returns a StreamedResponse', function (): void {
    $fake = WeasyPrintFacade::fake();

    $response = $fake->prepareSource('<p>test</p>')->download('test.pdf');

    expect($response->headers->get('content-type'))->toBe('application/pdf');
    expect($response->headers->get('content-disposition'))->toBe('attachment; filename=test.pdf');
  });

  test('inline returns a StreamedResponse', function (): void {
    $fake = WeasyPrintFacade::fake();

    $response = $fake->prepareSource('<p>test</p>')->inline('test.pdf');

    expect($response->headers->get('content-disposition'))->toBe('inline; filename=test.pdf');
  });

  test('stream passes custom headers through', function (): void {
    $fake = WeasyPrintFacade::fake();

    $response = $fake->prepareSource('<p>test</p>')->stream('test.pdf', ['X-Custom' => 'value']);

    expect($response->headers->get('X-Custom'))->toBe('value');
  });

  test('assertions are chainable', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>test</p>')->build();

    $result = $fake->assertSourcePrepared()->assertBuilt();

    expect($result)->toBe($fake);
  });
});
