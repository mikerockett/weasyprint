<?php

declare(strict_types=1);

namespace WeasyPrint\Tests\Fixtures;

class SampleHtml
{
  public static function simple(): string
  {
    return '<p>WeasyPrint rocks!</p>';
  }

  public static function withStyles(): string
  {
    return <<<HTML
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            p { color: #333; }
        </style>
    </head>
    <body>
        <p>Styled PDF content</p>
    </body>
    </html>
    HTML;
  }
}
