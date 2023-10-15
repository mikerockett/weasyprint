<?php

declare(strict_types=1);

namespace WeasyPrint\Pipeline;

interface BuildStage
{
  public function __invoke(BuildTraveler $container): BuildTraveler;
}
