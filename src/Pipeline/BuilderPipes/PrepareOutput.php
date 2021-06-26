<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\BuilderPipes;

use WeasyPrint\Exceptions\{MissingOutputFileException, OutputReadFailedException};
use WeasyPrint\Objects\Output;
use WeasyPrint\Pipeline\{BuilderContainer, BuilderPipelineStage};

class PrepareOutput implements BuilderPipelineStage
{
  public function __invoke(BuilderContainer $container): BuilderContainer
  {
    if (!is_file($outputPath = $container->getOutputPath())) {
      throw new MissingOutputFileException($outputPath);
    }

    if (!$output = file_get_contents($outputPath)) {
      unlink($outputPath);
      throw new OutputReadFailedException($outputPath);
    }

    unlink($outputPath);

    $container->setOutput(Output::new($output));

    return $container;
  }
}
