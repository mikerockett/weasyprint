<?php

declare(strict_types=1);

namespace WeasyPrint\Objects;

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\Exceptions\TemporaryFileException;

class Source
{
  protected array $attachments = [];

  private function __construct(protected Renderable|string $source)
  {}

  public static function new(Renderable|string $source): static
  {
    return new static($source);
  }

  public function get(): Renderable|string
  {
    return $this->source;
  }

  public function isUrl(): bool
  {
    if (gettype($source = $this->get()) !== 'string') {
      return false;
    }

    return filter_var($source, FILTER_VALIDATE_URL) !== false;
  }

  public function addAttachment(string $pathToAttachment): static
  {
    array_push($this->attachments, $pathToAttachment);

    return $this;
  }

  public function getAttachments(): array
  {
    return $this->attachments;
  }

  public function hasAttachments(): bool
  {
    return count($this->getAttachments()) > 0;
  }

  public function persistTemporaryFile(string $inputPath): void
  {
    if ($this->isUrl()) {
      return;
    }

    if (!file_put_contents($inputPath, $this->render())) {
      throw new TemporaryFileException($inputPath);
    }
  }

  public function render(): string
  {
    return $this->source instanceof Renderable
      ? $this->source->render()
      : $this->source;
  }
}
