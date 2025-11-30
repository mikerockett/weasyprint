<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline\Stages;

use Composer\Semver\Semver;
use WeasyPrint\Exceptions\UnsupportedVersionException;
use WeasyPrint\Pipeline\BuildStage;
use WeasyPrint\Pipeline\BuildTraveler;
use WeasyPrint\WeasyPrintFactory;

class AssertSupportedVersion implements BuildStage
{
  public function __invoke(BuildTraveler $container): BuildTraveler
  {
    $installed = $container->service->getWeasyPrintVersion();

    if (!Semver::satisfies($installed, WeasyPrintFactory::SUPPORTED_VERSIONS)) {
      throw new UnsupportedVersionException($installed);
    }

    return $container;
  }
}
