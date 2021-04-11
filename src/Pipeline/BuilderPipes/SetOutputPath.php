<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\BuilderPipes;

use WeasyPrint\Pipeline\{BuilderContainer, BuilderPipelineStage};

class SetOutputPath implements BuilderPipelineStage
{
  public function __invoke(BuilderContainer $container): BuilderContainer
  {
    $container->setOutputPath();

    return $container;
  }
}
