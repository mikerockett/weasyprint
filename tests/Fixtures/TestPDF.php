<?php

namespace WeasyPrint\Tests\Fixtures;

use Illuminate\Contracts\Support\Renderable;
use WeasyPrint\PDF;

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
