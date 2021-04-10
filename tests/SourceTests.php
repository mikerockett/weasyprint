<?php

declare(strict_types=1);

namespace WeasyPrint\Tests;

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\Objects\Source;
use WeasyPrint\Service;

class SourceTests extends TestCase
{
  /** @covers WeasyPrint\Service */
  public function testCanPrepareSourceFromSource(): void
  {
    $service = Service::new();

    $source = $service->prepareSource(
      Source::new('WeasyPrint rocks!')
    )->getSource();

    $this->assertInstanceOf(Source::class, $source);
    $this->assertIsString($source->get());
  }

  /** @covers WeasyPrint\Service */
  public function testCanPrepareSourceFromArguments(): void
  {
    $service = Service::new();

    $source = $service->prepareSource('WeasyPrint rocks!')->getSource();

    $this->assertInstanceOf(Source::class, $source);
    $this->assertIsString($source->get());
  }

  /** @covers WeasyPrint\Service */
  public function testCanPrepareSourceFromNamedArguments(): void
  {
    $service = Service::new();

    $source = $service->prepareSource(source: 'WeasyPrint rocks!')->getSource();

    $this->assertInstanceOf(Source::class, $source);
    $this->assertIsString($source->get());
  }

  /** @covers WeasyPrint\Service */
  public function testCanPrepareSourceFromRenderable(): void
  {
    $service = Service::new();

    $source = $service->prepareSource(view('test-pdf'))->getSource();

    $this->assertInstanceOf(Renderable::class, $source->get());
  }

  /** @covers WeasyPrint\Service */
  public function testCanPrepareSourceFromUrlAndVerify(): void
  {
    $service = Service::new();

    $source = $service->prepareSource('https://example.com')->getSource();

    $this->assertTrue($source->isUrl());
  }

  /** @covers WeasyPrint\Service */
  public function testCanPrepareSourceWithAttachments(): void
  {
    $service = Service::new();

    $source = $service->prepareSource('WeasyPrint rocks!')
      ->addAttachment($attachmentPath = __DIR__ . '/attachments/test-attachment.txt')
      ->getSource();

    $this->assertTrue($source->hasAttachments());
    $this->assertTrue($source->getAttachments()[0] === $attachmentPath);
  }
}
