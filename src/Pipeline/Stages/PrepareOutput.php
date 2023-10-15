<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\Stages;

use WeasyPrint\Exceptions\MissingOutputFileException;
use WeasyPrint\Exceptions\OutputReadFailedException;
use WeasyPrint\Objects\Output;
use WeasyPrint\Pipeline\BuildStage;
use WeasyPrint\Pipeline\BuildTraveler;

class PrepareOutput implements BuildStage
{
  public function __invoke(BuildTraveler $container): BuildTraveler
  {
    if (!is_file($outputPath = $container->getOutputPath())) {
      throw new MissingOutputFileException($outputPath);
    }

    if (!$output = file_get_contents($outputPath)) {
      unlink($outputPath);
      throw new OutputReadFailedException($outputPath);
    }

    unlink($outputPath);

    $container->setOutput(new Output($output));

    return $container;
  }
}
