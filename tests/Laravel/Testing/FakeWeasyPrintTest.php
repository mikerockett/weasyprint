<?php

declare(strict_types=1);

use WeasyPrint\Contracts\WeasyPrint;
use WeasyPrint\Enums\StreamMode;
use WeasyPrint\Exceptions\SourceNotSetException;
use WeasyPrint\Integration\Laravel\WeasyPrint as WeasyPrintFacade;
use WeasyPrint\Testing\FakeWeasyPrint;

describe('fake weasyprint', function (): void {
  it('swaps binding when faked', function (): void {
    $fake = WeasyPrintFacade::fake();

    expect($fake)->toBeInstanceOf(FakeWeasyPrint::class);
    expect(app(WeasyPrint::class))->toBe($fake);
  });

  it('returns fake output without invoking binary', function (): void {
    $fake = WeasyPrintFacade::fake();

    $output = $fake->prepareSource('<p>test</p>')->build();

    expect($output->data)->toContain('PDF');
  });

  it('returns fake data from getData', function (): void {
    $fake = WeasyPrintFacade::fake();

    $data = $fake->prepareSource('<p>test</p>')->getData();

    expect($data)->toBeString();
    expect($data)->toContain('PDF');
  });

  it('returns a fake version string', function (): void {
    $fake = WeasyPrintFacade::fake();

    expect($fake->getWeasyPrintVersion())->toBe('68.0');
  });

  it('passes assertBuilt when build was called', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>test</p>')->build();

    $fake->assertBuilt();
  });

  it('checks build count via assertBuilt', function (): void {
    $fake = WeasyPrintFacade::fake();
    $fake->prepareSource('<p>test</p>');

    $fake->build();
    $fake->build();

    $fake->assertBuilt(2);
  });

  it('passes assertNotBuilt when build was not called', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->assertNotBuilt();
  });

  it('passes assertSourcePrepared when source was prepared', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>hello</p>');

    $fake->assertSourcePrepared();
  });

  it('accepts a callback in assertSourcePrepared', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>hello</p>');

    $fake->assertSourcePrepared(function ($source) {
      expect($source)->toBe('<p>hello</p>');
    });
  });

  it('passes assertDownloaded when download was called', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>test</p>')->download('invoice.pdf');

    $fake->assertDownloaded();
    $fake->assertDownloaded('invoice.pdf');
  });

  it('passes assertInlined when inline was called', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>test</p>')->inline('report.pdf');

    $fake->assertInlined();
    $fake->assertInlined('report.pdf');
  });

  it('passes assertStreamed when stream was called', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>test</p>')->stream('doc.pdf', [], StreamMode::DOWNLOAD);

    $fake->assertStreamed();
    $fake->assertStreamed('doc.pdf');
    $fake->assertStreamed(mode: StreamMode::DOWNLOAD);
  });

  it('passes assertAttachmentAdded when attachment was added', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>test</p>')->addAttachment('/path/to/file.xml', 'Data');

    $fake->assertAttachmentAdded();
    $fake->assertAttachmentAdded('/path/to/file.xml');
  });

  it('throws SourceNotSetException when adding attachment without source', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->addAttachment('/path/to/file.xml');
  })->throws(SourceNotSetException::class);

  it('passes assertXmpMetadataAdded when xmp metadata was added', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->addXmpMetadata('/path/to/rdf.xml');

    $fake->assertXmpMetadataAdded();
    $fake->assertXmpMetadataAdded('/path/to/rdf.xml');
  });

  it('returns a StreamedResponse from download', function (): void {
    $fake = WeasyPrintFacade::fake();

    $response = $fake->prepareSource('<p>test</p>')->download('test.pdf');

    expect($response->headers->get('content-type'))->toBe('application/pdf');
    expect($response->headers->get('content-disposition'))->toBe('attachment; filename=test.pdf');
  });

  it('returns a StreamedResponse from inline', function (): void {
    $fake = WeasyPrintFacade::fake();

    $response = $fake->prepareSource('<p>test</p>')->inline('test.pdf');

    expect($response->headers->get('content-disposition'))->toBe('inline; filename=test.pdf');
  });

  it('passes custom headers through when streaming', function (): void {
    $fake = WeasyPrintFacade::fake();

    $response = $fake->prepareSource('<p>test</p>')->stream('test.pdf', ['X-Custom' => 'value']);

    expect($response->headers->get('X-Custom'))->toBe('value');
  });

  it('supports chaining assertions', function (): void {
    $fake = WeasyPrintFacade::fake();

    $fake->prepareSource('<p>test</p>')->build();

    $result = $fake->assertSourcePrepared()->assertBuilt();

    expect($result)->toBe($fake);
  });
});
