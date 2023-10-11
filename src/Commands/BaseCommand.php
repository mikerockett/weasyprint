<?php

declare(strict_types=1);

namespace WeasyPrint\Commands;

use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;
use WeasyPrint\Objects\Config;

class BaseCommand implements Command
{
  protected Config $config;
  protected Collection $arguments;

  public function maybePushArgument(string $key, $value): void
  {
    $key = "--$key";

    if ($value === true) {
      $this->arguments->push($key);
    } elseif ($value) {
      $this->arguments->push($key, $value);
    }
  }

  public function execute(): string
  {
    $process = new Process(
      command: $this->arguments->toArray(),
      env: $this->config->getProcessEnvironment(),
      timeout: $this->config->getTimeout()
    );

    $process->mustRun();

    return $process->getOutput();
  }
}
