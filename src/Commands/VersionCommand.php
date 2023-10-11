<?php

declare(strict_types=1);

namespace WeasyPrint\Commands;

use Illuminate\Support\Collection;
use WeasyPrint\Objects\Config;

class VersionCommand extends BaseCommand
{
  use FindsBinary;

  public function __construct(
    Config $config,
  ) {
    $this->config = $config;

    $this->arguments = new Collection([
      $config->getBinary() ?? $this->findBinary(),
      '--version',
    ]);
  }
}
