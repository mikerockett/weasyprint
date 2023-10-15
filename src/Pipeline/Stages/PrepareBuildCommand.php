<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\Stages;

use WeasyPrint\Commands\BuildCommand;
use WeasyPrint\Pipeline\BuildStage;
use WeasyPrint\Pipeline\BuildTraveler;

class PrepareBuildCommand implements BuildStage
{
  public function __invoke(BuildTraveler $container): BuildTraveler
  {
    $service = $container->service;

    $container->setCommand(new BuildCommand(
      config: $service->getConfig(),
      inputPath: $container->getInputPath(),
      outputPath: $container->getOutputPath(),
      attachments: $service->getSource()->getAttachments()
    ));

    return $container;
  }
}
