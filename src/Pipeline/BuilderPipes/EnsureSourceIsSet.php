<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\BuilderPipes;

use WeasyPrint\Exceptions\SourceNotSetException;
use WeasyPrint\Pipeline\{BuilderContainer, BuilderPipelineStage};

class EnsureSourceIsSet implements BuilderPipelineStage
{
  public function __invoke(BuilderContainer $container): BuilderContainer
  {
    if (!$container->service->sourceIsSet()) {
      throw new SourceNotSetException;
    }

    return $container;
  }
}
