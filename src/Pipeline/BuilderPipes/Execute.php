<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\BuilderPipes;

use WeasyPrint\Pipeline\{BuilderContainer, BuilderPipelineStage};

class Execute implements BuilderPipelineStage
{
  public function __invoke(BuilderContainer $container): BuilderContainer
  {
    $container->getCommand()->execute();

    return $container;
  }
}
