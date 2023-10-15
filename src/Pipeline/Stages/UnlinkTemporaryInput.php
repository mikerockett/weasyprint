<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\Stages;

use WeasyPrint\Pipeline\BuildStage;
use WeasyPrint\Pipeline\BuildTraveler;

class UnlinkTemporaryInput implements BuildStage
{
  public function __invoke(BuildTraveler $container): BuildTraveler
  {
    if (is_file($inputPath = $container->getInputPath())) {
      unlink($inputPath);
    }

    return $container;
  }
}
