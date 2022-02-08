<?php

declare(strict_types=1);

namespace WeasyPrint\Tests\Fixtures;

use Illuminate\Contracts\Support\Arrayable;

class SourceData implements Arrayable
{
  public function __construct(protected string $test = 'foo')
  {}

  public function toArray()
  {
    return [
      'test' => $this->test,
    ];
  }
}
