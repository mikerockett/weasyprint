<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\BuilderPipes;

use WeasyPrint\Pipeline\{BuilderContainer, BuilderPipelineStage};

class SetInputPath implements BuilderPipelineStage
{
  public function __invoke(BuilderContainer $container): BuilderContainer
  {
    $container->setInputPath(
      ($source = $container->service->getSource())->isUrl()
        ? $source->get()
        : $container->makeTemporaryFilename()
    );

    return $container;
  }
}
