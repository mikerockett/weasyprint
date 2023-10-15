<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\Stages;

use WeasyPrint\Exceptions\SourceNotSetException;
use WeasyPrint\Pipeline\BuildStage;
use WeasyPrint\Pipeline\BuildTraveler;

class EnsureSourceIsSet implements BuildStage
{
  public function __invoke(BuildTraveler $container): BuildTraveler
  {
    if (!$container->service->sourceIsSet()) {
      throw new SourceNotSetException();
    }

    return $container;
  }
}
