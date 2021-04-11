<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline;

interface BuilderPipelineStage
{
  public function __invoke(BuilderContainer $container): BuilderContainer;
}
