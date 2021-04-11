<?php

declare(strict_types=1);

namespace WeasyPrint;

use Illuminate\Support\Facades\Facade as SupportFacade;
use WeasyPrint\Contracts\Factory;
use WeasyPrint\Objects\Config;

/**
 * @method Factory mergeConfig(mixed ...$config)
 * @method Factory prepareSource(Source|Renderable|string $source)
 * @method Factory to(OutputType $outputType)
 * @method Factory toPdf()
 * @method Factory toPng()
 * @method Config getConfig()
 */
class Facade extends SupportFacade
{
  public static function getFacadeAccessor(): string
  {
    return Factory::class;
  }
}
