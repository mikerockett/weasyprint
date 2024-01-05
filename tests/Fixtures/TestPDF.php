<?php

namespace WeasyPrint\Tests\Fixtures;

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\PDF;

class TestPDF extends PDF
{
  protected string $filename = 'test.pdf';

  public function source(): Renderable
  {
    return view('test-pdf');
  }
}
