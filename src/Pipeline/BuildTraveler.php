<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline;

use WeasyPrint\Commands\BuildCommand;
use WeasyPrint\Objects\Output;
use WeasyPrint\WeasyPrintFactory;

class BuildTraveler
{
  private ?string $inputPath = null;
  private ?string $outputPath = null;
  private BuildCommand $command;
  private Output $output;

  public function __construct(
    public WeasyPrintFactory $service,
  ) {}

  public function makeTemporaryFilename(): string|false
  {
    return tempnam(
      directory: sys_get_temp_dir(),
      prefix: $this->service->getConfig()->cachePrefix,
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

  private function cleanupPath(?string $path): void
  {
    if ($path === null || !is_file($path)) {
      return;
    }

    rescue(fn() => unlink($path));
  }

  public function cleanupInputPath(): void
  {
    $this->cleanupPath($this->inputPath);
  }

  public function cleanupOutputPath(): void
  {
    $this->cleanupPath($this->outputPath);
  }

  public function cleanupTemporaryPaths(): void
  {
    $this->cleanupInputPath();
    $this->cleanupOutputPath();
  }
}
