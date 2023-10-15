<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\Stages;

use WeasyPrint\Pipeline\BuildStage;
use WeasyPrint\Pipeline\BuildTraveler;

class Execute implements BuildStage
{
  public function __invoke(BuildTraveler $container): BuildTraveler
  {
    $container->getCommand()->execute();

    return $container;
  }
}
