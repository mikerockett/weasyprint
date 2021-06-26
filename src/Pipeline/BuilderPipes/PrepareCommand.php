<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\BuilderPipes;

use WeasyPrint\Command;
use WeasyPrint\Pipeline\{BuilderContainer, BuilderPipelineStage};

class PrepareCommand implements BuilderPipelineStage
{
  public function __invoke(BuilderContainer $container): BuilderContainer
  {
    $service = $container->service;

    $container->setCommand(new Command(
      config: $service->getConfig(),
      inputPath: $container->getInputPath(),
      outputPath: $container->getOutputPath(),
      attachments: $service->getSource()->getAttachments()
    ));

    return $container;
  }
}
