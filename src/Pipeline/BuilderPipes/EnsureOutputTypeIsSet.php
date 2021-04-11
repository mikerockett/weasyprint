<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\BuilderPipes;

use WeasyPrint\Enums\OutputType;
use WeasyPrint\Pipeline\{BuilderContainer, BuilderPipelineStage};

class EnsureOutputTypeIsSet implements BuilderPipelineStage
{
  public function __invoke(BuilderContainer $container): BuilderContainer
  {
    if ($container->service->getOutputType()->is(OutputType::none())) {
      $container->service->setOutputType(OutputType::pdf());
    }

    return $container;
  }
}
