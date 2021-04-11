<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\BuilderPipes;

use WeasyPrint\Pipeline\{BuilderContainer, BuilderPipelineStage};

class UnlinkTemporaryInput implements BuilderPipelineStage
{
  public function __invoke(BuilderContainer $container): BuilderContainer
  {
    if (is_file($inputPath = $container->getInputPath())) {
      unlink($inputPath);
    }

    return $container;
  }
}
