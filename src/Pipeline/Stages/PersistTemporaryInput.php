<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\Stages;

use WeasyPrint\Pipeline\BuildStage;
use WeasyPrint\Pipeline\BuildTraveler;

class PersistTemporaryInput implements BuildStage
{
  public function __invoke(BuildTraveler $container): BuildTraveler
  {
    $container->service
      ->getSource()
      ->persistTemporaryFile($container->getInputPath());

    return $container;
  }
}
