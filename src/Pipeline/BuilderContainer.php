<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline;

use WeasyPrint\Commands\BuildCommand;
use WeasyPrint\Objects\Output;
use WeasyPrint\Service;

class BuilderContainer
{
  private string $inputPath;
  private string $outputPath;
  private BuildCommand $command;
  private Output $output;

  public function __construct(
    public Service $service
  ) {
  }

  public function makeTemporaryFilename(): string|false
  {
    return tempnam(
      sys_get_temp_dir(),
      $this->service->getConfig()->getCachePrefix()
    );
  }

  public function setInputPath(string $inputPath): void
  {
    $this->inputPath = $inputPath;
  }

  public function setOutputPath(): void
  {
    $this->outputPath = $this->makeTemporaryFilename();
  }

  public function setCommand(BuildCommand $command): void
  {
    $this->command = $command;
  }

  public function setOutput(Output $output): void
  {
    $this->output = $output;
  }

  public function getInputPath(): string
  {
    return $this->inputPath;
  }

  public function getOutputPath(): string
  {
    return $this->outputPath;
  }

  public function getCommand(): BuildCommand
  {
    return $this->command;
  }

  public function getOutput(): Output
  {
    return $this->output;
  }
}
