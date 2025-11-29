<?php

declare(strict_types=1);

namespace WeasyPrint\Tests\Fixtures;

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\Integration\Laravel\PDF;

class TestPDF extends PDF
{
  public function source(): Renderable
  {
    return view('test-pdf');
  }

  public function filename(): string
  {
    return 'test.pdf';
  }
}
