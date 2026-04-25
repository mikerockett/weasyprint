<?php

declare(strict_types=1);

namespace WeasyPrint\Commands;

use WeasyPrint\Exceptions\BinaryNotFoundException;
use Symfony\Component\Process\Process;

trait FindsBinary
{
  private function findBinary(): string
  {
    return collect([
      '/usr/bin/weasyprint',
      '/usr/local/bin/weasyprint',
      '/bin/weasyprint',
      '/usr/sbin/weasyprint',
      '/sbin/weasyprint',
      '/opt/homebrew/bin/weasyprint',
      '/opt/local/bin/weasyprint',
    ])->first(
      fn(string $path): bool => is_executable($path),
      fn() => $this->whichBinary(),
    );
  }

  private function whichBinary(): string
  {
    $process = Process::fromShellCommandline('which weasyprint');
    $process->run();

    if (!$process->isSuccessful()) {
      throw new BinaryNotFoundException();
    }

    $path = trim($process->getOutput());

    if (!is_executable($path)) {
      throw new BinaryNotFoundException();
    }

    return $path;
  }
}
