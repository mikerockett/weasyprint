<?php

namespace WeasyPrint\Commands;

use RuntimeException;
use Symfony\Component\Process\Process;

trait FindsBinary
{
  private function findBinary(): string
  {
    $process = Process::fromShellCommandline('which weasyprint');
    $process->run();

    if (!$process->isSuccessful()) {
      throw new RuntimeException(
        'Unable to find WeasyPrint binary. Please specify the absolute path to WeasyPrint in config [binary].'
      );
    }

    return trim($process->getOutput());
  }
}
