<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\Stages;

use WeasyPrint\Pipeline\BuildStage;
use WeasyPrint\Pipeline\BuildTraveler;

class SetInputPath implements BuildStage
{
  public function __invoke(BuildTraveler $container): BuildTraveler
  {
    $container->setInputPath(
      match (($source = $container->service->getSource())->isUrl()) {
        true => $source->get(),
        default => $container->makeTemporaryFilename()
      }
    );

    return $container;
  }
}
