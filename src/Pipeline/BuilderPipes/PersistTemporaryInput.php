<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\BuilderPipes;

use WeasyPrint\Pipeline\{BuilderContainer, BuilderPipelineStage};

class PersistTemporaryInput implements BuilderPipelineStage
{
  public function __invoke(BuilderContainer $container): BuilderContainer
  {
    $container->service
      ->getSource()
      ->persistTemporaryFile($container->getInputPath());

    return $container;
  }
}
